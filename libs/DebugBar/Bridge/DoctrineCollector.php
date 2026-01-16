<?php

declare(strict_types=1);

/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge;

use DateTimeInterface;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DebugBarException;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;

/**
 * Collects Doctrine queries
 *
 * http://doctrine-project.org
 *
 * Uses the DebugStack logger to collects data about queries
 *
 * <code>
 * $debugStack = new Doctrine\DBAL\Logging\DebugStack();
 * $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
 * $debugbar->addCollector(new DoctrineCollector($debugStack));
 * </code>
 */
class DoctrineCollector extends DataCollector implements AssetProvider, Renderable
{
    protected $debugStack;

    /**
     * DoctrineCollector constructor.
     *
     * @throws DebugBarException
     */
    public function __construct($debugStackOrEntityManager)
    {
        if ($debugStackOrEntityManager instanceof EntityManager) {
            $debugStackOrEntityManager = $debugStackOrEntityManager->getConnection()->getConfiguration()->getSQLLogger();
        }
        if (! ($debugStackOrEntityManager instanceof DebugStack)) {
            throw new DebugBarException("'DoctrineCollector' requires an 'EntityManager' or 'DebugStack' object");
        }
        $this->debugStack = $debugStackOrEntityManager;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $queries = [];
        $totalExecTime = 0;
        foreach ($this->debugStack->queries as $q) {
            $queries[] = [
                'sql' => $q['sql'],
                'params' => (object) $this->getParameters($q['params'] ?? []),
                'duration' => $q['executionMS'],
                'duration_str' => $this->formatDuration($q['executionMS']),
            ];
            $totalExecTime += $q['executionMS'];
        }

        return [
            'nb_statements' => count($queries),
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries,
        ];
    }

    /**
     * Returns an array of parameters used with the query
     */
    public function getParameters($params): array
    {
        return array_map(function ($param) {
            if (is_string($param)) {
                return htmlentities($param, ENT_QUOTES, 'UTF-8', false);
            } elseif (is_array($param)) {
                return '['.implode(', ', $this->getParameters($param)).']';
            } elseif (is_numeric($param)) {
                return strval($param);
            } elseif ($param instanceof DateTimeInterface) {
                return $param->format('Y-m-d H:i:s');
            } elseif (is_object($param)) {
                return json_encode($param);
            }

            return $param ?: '';
        }, $params);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return [
            'database' => [
                'icon' => 'arrow-right',
                'widget' => 'PhpDebugBar.Widgets.SQLQueriesWidget',
                'map' => 'doctrine',
                'default' => '[]',
            ],
            'database:badge' => [
                'map' => 'doctrine.nb_statements',
                'default' => 0,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return [
            'css' => 'widgets/sqlqueries/widget.css',
            'js' => 'widgets/sqlqueries/widget.js',
        ];
    }
}

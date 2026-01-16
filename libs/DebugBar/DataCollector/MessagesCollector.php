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

namespace DebugBar\DataCollector;

use BackedEnum;
use DateTimeInterface;
use DebugBar\DataFormatter\HasDataFormatter;
use DebugBar\DataFormatter\HasXdebugLinks;
use Psr\Log\AbstractLogger;
use UnitEnum;

/**
 * Provides a way to log messages
 */
class MessagesCollector extends AbstractLogger implements AssetProvider, DataCollectorInterface, MessagesAggregateInterface, Renderable
{
    use HasDataFormatter;
    use HasXdebugLinks;

    protected $name;

    protected $messages = [];

    protected $aggregates = [];

    /** @var bool */
    protected $collectFile = false;

    /**
     * @param string $name
     */
    public function __construct($name = 'messages')
    {
        $this->name = $name;
    }

    /** @return void */
    public function collectFileTrace($enabled = true)
    {
        $this->collectFile = $enabled;
    }

    /**
     * @param string|null $messageHtml
     * @param mixed       $message
     *
     * @return string|null
     */
    protected function customizeMessageHtml($messageHtml, $message)
    {
        $pos = strpos((string) $messageHtml, 'sf-dump-expanded');
        if ($pos !== false) {
            $messageHtml = substr_replace($messageHtml, 'sf-dump-compact', $pos, 16);
        }

        return $messageHtml;
    }

    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     *
     * @param mixed  $message
     * @param string $label
     */
    public function addMessage($message, $label = 'info', $isString = true)
    {
        $messageText = $message;
        $messageHtml = null;
        if (! is_string($message)) {
            // Send both text and HTML representations; the text version is used for searches
            $messageText = $this->getDataFormatter()->formatVar($message);
            if ($this->isHtmlVarDumperUsed()) {
                $messageHtml = $this->getVarDumper()->renderVar($message);
            }
            $isString = false;
        }

        $stackItem = [];
        if ($this->collectFile) {
            $stacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $stackItem = $stacktrace[0];
            foreach ($stacktrace as $trace) {
                if (! isset($trace['file']) || strpos($trace['file'], '/vendor/') !== false) {
                    continue;
                }

                $stackItem = $trace;
                break;
            }
        }

        $this->messages[] = [
            'message' => $messageText,
            'message_html' => $this->customizeMessageHtml($messageHtml, $message),
            'is_string' => $isString,
            'label' => $label,
            'time' => microtime(true),
            'xdebug_link' => $stackItem ? $this->getXdebugLink($stackItem['file'], $stackItem['line'] ?? null) : null,
        ];
    }

    /**
     * Aggregates messages from other collectors
     */
    public function aggregate(MessagesAggregateInterface $messages)
    {
        if ($this->collectFile && method_exists($messages, 'collectFileTrace')) {
            $messages->collectFileTrace();
        }

        $this->aggregates[] = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->messages;
        foreach ($this->aggregates as $collector) {
            $msgs = array_map(function ($m) use ($collector) {
                $m['collector'] = $collector->getName();

                return $m;
            }, $collector->getMessages());
            $messages = array_merge($messages, $msgs);
        }

        // sort messages by their timestamp
        usort($messages, function ($a, $b) {
            if ($a['time'] === $b['time']) {
                return 0;
            }

            return $a['time'] < $b['time'] ? -1 : 1;
        });

        return $messages;
    }

    public function log($level, $message, array $context = []): void
    {
        // For string messages, interpolate the context following PSR-3
        if (is_string($message)) {
            $message = $this->interpolate($message, $context);
        }
        $this->addMessage($message, $level);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     *
     * @return string
     */
    public function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            $placeholder = '{'.$key.'}';
            if (strpos($message, $placeholder) === false) {
                continue;
            }
            // check that the value can be cast to string
            if ($val === null || is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace[$placeholder] = $val;
            } elseif ($val instanceof DateTimeInterface) {
                $replace[$placeholder] = $val->format("Y-m-d\TH:i:s.uP");
            } elseif ($val instanceof UnitEnum) {
                $replace[$placeholder] = $val instanceof BackedEnum ? $val->value : $val->name;
            } elseif (is_object($val)) {
                $replace[$placeholder] = '[object '.$this->getDataFormatter()->formatClassName($val).']';
            } elseif (is_array($val)) {
                $json = @json_encode($val);
                $replace[$placeholder] = $json === false ? 'null' : 'array'.$json;
            } else {
                $replace[$placeholder] = '['.gettype($val).']';
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Deletes all messages
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * @return array
     */
    public function collect()
    {
        $messages = $this->getMessages();

        return [
            'count' => count($messages),
            'messages' => $messages,
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : [];
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();

        return [
            "$name" => [
                'icon' => 'list-alt',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => "$name.messages",
                'default' => '[]',
            ],
            "$name:badge" => [
                'map' => "$name.count",
                'default' => 'null',
            ],
        ];
    }
}

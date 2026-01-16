<?php

declare(strict_types=1);

/**
 * The class property $layout_view_model must be defined in the BaseController
 * The class property $container must be defined in the BaseController
 */

namespace App\Core\Traits;

use Core\Views\ViewInterface;
use Helpers\File\Paths;
use Helpers\Http\Response;
use InvalidArgumentException;

trait ViewHandlerTrait
{
    /**
     * Gets the module name from the controller's namespace.
     */
    private static function getModuleName(): string
    {
        $parts = explode('\\', static::class);

        if (count($parts) < 2) {
            return '';
        }

        return $parts[1];
    }

    /**
     * Renders a view template and returns a Response object.
     */
    protected function asView(string $template, array $data = [], ?callable $callback = null): Response
    {
        $view_data = array_merge($data, [
            'layout' => $this->layout_view_model,
        ]);

        $view_engine = $this->container->get(ViewInterface::class);
        $deny = [
            'value' => false,
        ];

        if ($callback) {
            $callback_result = $callback();

            $is_bool = is_bool($callback_result);
            $is_array = is_array($callback_result);

            if (! $is_bool && ! $is_array) {
                throw new InvalidArgumentException('Callback must return a boolean or an array.');
            }

            if ($is_array) {
                $has_value = array_key_exists('value', $callback_result);
                $has_template = array_key_exists('template', $callback_result);

                if (! $has_value || ! $has_template) {
                    throw new InvalidArgumentException('Callback array must contain both "value" and "template" keys.');
                }

                $deny = $callback_result;

            } elseif ($is_bool) {
                $deny['value'] = $callback_result;
            }
        }

        return $view_engine->path(Paths::templatePath(module: static::getModuleName()))
            ->template($template)
            ->data($view_data)
            ->denyAccessIf(...$deny)
            ->render();
    }
}

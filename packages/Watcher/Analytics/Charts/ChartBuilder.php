<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Builder for chart data structures.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Analytics\Charts;

class ChartBuilder
{
    public static function lineChart(array $data, string $label = 'Data'): array
    {
        $labels = array_keys($data);
        $values = array_values($data);

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $label,
                        'data' => $values,
                    ],
                ],
            ],
        ];
    }

    public static function barChart(array $data, string $label = 'Data'): array
    {
        $labels = array_keys($data);
        $values = array_values($data);

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $label,
                        'data' => $values,
                    ],
                ],
            ],
        ];
    }

    public static function pieChart(array $data): array
    {
        $labels = array_keys($data);
        $values = array_values($data);

        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $values,
                    ],
                ],
            ],
        ];
    }

    public static function toJson(array $chartData): string
    {
        return json_encode($chartData, JSON_PRETTY_PRINT);
    }
}

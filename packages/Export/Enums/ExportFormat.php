<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Export format enum.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Enums;

enum ExportFormat: string
{
    case CSV = 'csv';
    case XLSX = 'xlsx';
    case PDF = 'pdf';
    case JSON = 'json';
}

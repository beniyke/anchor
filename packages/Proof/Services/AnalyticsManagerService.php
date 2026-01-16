<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Analytics Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Services;

use Audit\Audit;
use Database\BaseModel;
use Proof\Models\Testimonial;

/**
 * Handles analytics for testimonials and case studies.
 */
class AnalyticsManagerService
{
    /**
     * Record a view for a testimonial or case study.
     */
    public function recordView(BaseModel $model, array $metadata = []): void
    {
        $type = $model instanceof Testimonial ? 'testimonial' : 'case_study';

        if (class_exists('Audit\Audit')) {
            Audit::log(
                "proof.{$type}.view",
                array_merge($metadata, [
                    'ip' => $metadata['ip'] ?? null,
                ]),
                $model
            );
        }
    }

    public function getMetrics(BaseModel $model): array
    {
        // This would typically query a dedicated analytics table or Audit log
        return [
            'views' => 0, // Placeholder
            'clicks' => 0,
        ];
    }
}

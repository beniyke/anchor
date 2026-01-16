<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Proof.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof;

use Proof\Models\CaseStudy;
use Proof\Models\ProofRequest;
use Proof\Models\Source;
use Proof\Models\Testimonial;
use Proof\Services\AnalyticsManagerService;
use Proof\Services\Builders\CaseStudyBuilder;
use Proof\Services\Builders\TestimonialBuilder;
use Proof\Services\ProofManagerService;

/**
 * Static proxy for the Proof package.
 */
class Proof
{
    /**
     * Start building a new testimonial.
     */
    public static function testimonial(?Testimonial $testimonial = null): TestimonialBuilder
    {
        return resolve(ProofManagerService::class)->testimonial($testimonial);
    }

    /**
     * Start building a new case study.
     */
    public static function caseStudy(?CaseStudy $caseStudy = null): CaseStudyBuilder
    {
        return resolve(CaseStudyBuilder::class)->setInstance($caseStudy);
    }

    public static function createSource(array $data): Source
    {
        return resolve(ProofManagerService::class)->createSource($data);
    }

    public static function findTestimonial(int $id): ?Testimonial
    {
        return resolve(ProofManagerService::class)->findTestimonial($id);
    }

    public static function approve(int|string $id): bool
    {
        return resolve(ProofManagerService::class)->approve((int) $id);
    }

    public static function reject(int|string $id): bool
    {
        return resolve(ProofManagerService::class)->reject((int) $id);
    }

    /**
     * Attach media.
     */
    public static function attachMedia(Testimonial|CaseStudy $model, int $mediaId, string $type = 'photo'): void
    {
        resolve(ProofManagerService::class)->attachMedia($model, $mediaId, $type);
    }

    public static function request(Source $source): ProofRequest
    {
        return resolve(ProofManagerService::class)->createRequest($source);
    }

    public static function collectionUrl(ProofRequest $request): string
    {
        return resolve(ProofManagerService::class)->generateCollectionUrl($request);
    }

    public static function fromSubmission(object $submission, array $mapping = []): ?Testimonial
    {
        return resolve(ProofManagerService::class)->fromStackSubmission($submission, $mapping);
    }

    public static function analytics(): AnalyticsManagerService
    {
        return resolve(AnalyticsManagerService::class);
    }
}

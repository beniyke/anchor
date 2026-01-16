<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Proof Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Services;

use Audit\Audit;
use Helpers\DateTimeHelper;
use Link\Link;
use Media\Media;
use Proof\Models\CaseStudy;
use Proof\Models\ProofRequest;
use Proof\Models\Source;
use Proof\Models\Testimonial;
use Proof\Services\Builders\CaseStudyBuilder;
use Proof\Services\Builders\TestimonialBuilder;
use Proof\Workflows\TestimonialApprovalWorkflow;
use Stack\Stack;
use Workflow\Contracts\History;
use Workflow\Workflow;

/**
 * Primary manager for the Proof package.
 */
class ProofManagerService
{
    /**
     * Start building a new testimonial.
     */
    public function testimonial(?Testimonial $testimonial = null): TestimonialBuilder
    {
        return resolve(TestimonialBuilder::class)->setInstance($testimonial);
    }

    /**
     * Start building a new case study.
     */
    public function caseStudy(?CaseStudy $caseStudy = null): CaseStudyBuilder
    {
        return resolve(CaseStudyBuilder::class)->setInstance($caseStudy);
    }

    public function findTestimonial(int $id): ?Testimonial
    {
        return Testimonial::find($id);
    }

    public function createSource(array $data): Source
    {
        $source = Source::create($data);

        if (class_exists('Audit\Audit')) {
            Audit::log('proof.source.created', ['id' => $source->id, 'name' => $source->name], $source);
        }

        return $source;
    }

    /**
     * Create a collection request for a source.
     */
    public function createRequest(Source $source): ProofRequest
    {
        $request = ProofRequest::create([
            'proof_source_id' => $source->id,
            'token' => bin2hex(random_bytes(32)),
            'status' => 'sent',
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('proof.request.created', ['id' => $request->id, 'email' => $source->email], $request);
        }

        return $request;
    }

    public function generateCollectionUrl(ProofRequest $request): string
    {
        $expiry = config('proof.request.expiry_days', 7);

        if (class_exists('Link\Link')) {
            return Link::create("proof/collect/{$request->token}")
                ->expires(DateTimeHelper::now()->addDays($expiry))
                ->url();
        }

        return url("proof/collect/{$request->token}");
    }

    public function fromStackSubmission(object $submission, array $mapping = []): ?Testimonial
    {
        if (!config('proof.form_integration', true)) {
            return null;
        }

        if (!class_exists('Stack\Stack')) {
            return null;
        }

        $values = Stack::getSubmissionData($submission);

        $source = $this->createSource([
            'name' => $values[$mapping['name'] ?? 'name'] ?? 'Anonymous',
            'email' => $values[$mapping['email'] ?? 'email'] ?? null,
            'company' => $values[$mapping['company'] ?? 'company'] ?? null,
        ]);

        return $this->testimonial()
            ->source($source)
            ->content($values[$mapping['content'] ?? 'content'] ?? '')
            ->rating((int)($values[$mapping['rating'] ?? 'rating'] ?? 5))
            ->status('pending')
            ->save();
    }

    /**
     * Attach media to a testimonial or case study.
     */
    public function attachMedia(Testimonial|CaseStudy $model, int $mediaId, string $type = 'photo'): void
    {
        if (class_exists('Media\Media')) {
            $media = Media::find($mediaId);
            if ($media) {
                if ($model instanceof Testimonial) {
                    $model->update([$type === 'video' ? 'video_url' : 'avatar_url' => Media::url($media)]);
                } else {
                    $model->update(['featured_image' => Media::url($media)]);
                }
            }
        }
    }

    /**
     * Start the approval workflow for a testimonial.
     */
    public function startApprovalWorkflow(Testimonial $testimonial): string
    {
        if (class_exists('Workflow\Workflow')) {
            return Workflow::run(TestimonialApprovalWorkflow::class, [
                'testimonial_id' => $testimonial->id,
            ], "proof_approval_{$testimonial->id}");
        }

        return '';
    }

    public function approve(int $id): bool
    {
        $testimonial = $this->findTestimonial($id);
        if (!$testimonial) {
            return false;
        }

        $testimonial->update(['status' => 'approved']);

        if (class_exists('Workflow\Workflow')) {
            $history = resolve(History::class);
            $instanceId = $history->findActiveInstanceIdByBusinessKey("proof_approval_{$id}");
            if ($instanceId) {
                $history->recordEvent($instanceId, 'SignalReceived', [
                    'name' => 'approved',
                    'payload' => [],
                ]);
                Workflow::execute($instanceId);
            }
        }

        if (class_exists('Audit\Audit')) {
            Audit::log('proof.testimonial.approved', ['id' => $id], $testimonial);
        }

        return true;
    }

    public function reject(int $id): bool
    {
        $testimonial = $this->findTestimonial($id);
        if (!$testimonial) {
            return false;
        }

        $testimonial->update(['status' => 'rejected']);

        if (class_exists('Workflow\Workflow')) {
            $history = resolve(History::class);
            $instanceId = $history->findActiveInstanceIdByBusinessKey("proof_approval_{$id}");
            if ($instanceId) {
                $history->recordEvent($instanceId, 'SignalReceived', [
                    'name' => 'rejected',
                    'payload' => [],
                ]);
                Workflow::execute($instanceId);
            }
        }

        if (class_exists('Audit\Audit')) {
            Audit::log('proof.testimonial.rejected', ['id' => $id], $testimonial);
        }

        return true;
    }

    /**
     * For internal use by builders.
     */
    public function saveTestimonial(array $data, ?Testimonial $instance = null): Testimonial
    {
        if ($instance) {
            $instance->update($data);

            return $instance;
        }

        return Testimonial::create($data);
    }

    /**
     * For internal use by builders.
     */
    public function saveCaseStudy(array $data, ?CaseStudy $instance = null): CaseStudy
    {
        if ($instance) {
            $instance->update($data);

            return $instance;
        }

        return CaseStudy::create($data);
    }
}

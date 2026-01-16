<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Testimonial Approval Workflow.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Workflows;

use Generator;
use Proof\Models\Testimonial;
use Proof\Proof;
use Workflow\Contracts\Workflow;

/**
 * Handles the approval lifecycle of a testimonial.
 */
class TestimonialApprovalWorkflow implements Workflow
{
    /**
     * The main execution logic of the workflow.
     */
    public function execute(array $input): Generator
    {
        $id = $input['testimonial_id'] ?? null;

        if (!$id) {
            return "Error: No testimonial ID provided";
        }

        $testimonial = Proof::findTestimonial((int)$id);

        if (!$testimonial) {
            return "Error: Testimonial not found";
        }

        // Wait for approval signal
        $signal = yield;

        if ($signal['name'] === 'approved') {
            $testimonial->update(['status' => 'approved']);

            return "Testimonial approved";
        }

        if ($signal['name'] === 'rejected') {
            $testimonial->update(['status' => 'rejected']);

            return "Testimonial rejected";
        }

        return "Workflow completed without action";
    }

    /**
     * Handle external signals sent to the workflow.
     */
    public function handleSignal(string $signalName, array $payload): void
    {
        // This is handled by the yield in execute for this simple case
    }
}

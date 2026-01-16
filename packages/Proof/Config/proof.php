<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * proof.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /**
     * Default status for newly created testimonials.
     */
    'default_status' => 'pending',

    /**
     * Enable automatic conversion of form submissions to testimonials.
     */
    'form_integration' => true,

    /**
     * Rating scale (e.g., 1-5).
     */
    'rating_scale' => [
        'min' => 1,
        'max' => 5,
    ],

    /**
     * Approval settings.
     */
    'approval' => [
        'required' => true,
        'notify_on_submission' => true,
    ],

    /**
     * Request settings.
     */
    'request' => [
        'expiry_days' => 7,
    ],
];

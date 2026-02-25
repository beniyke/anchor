<?php

declare(strict_types=1);

namespace Academy\Contracts;

use Academy\Models\AcademyCertificate;
use Academy\Models\AcademyEnrolment;

interface CertificateGeneratorInterface
{
    /**
     * Generate a certificate for the given enrolment.
     *
     * @param AcademyEnrolment $enrolment
     *
     * @return AcademyCertificate
     */
    public function generate(AcademyEnrolment $enrolment): AcademyCertificate;

    /**
     * Generate a transcript for the given enrolment.
     *
     * @param AcademyEnrolment $enrolment
     *
     * @return string Path to generated file
     */
    public function generateTranscript(AcademyEnrolment $enrolment): string;
}

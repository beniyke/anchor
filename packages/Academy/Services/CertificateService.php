<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\CertificateStatus;
use Academy\Exceptions\AccessDeniedException;
use Academy\Models\AcademyCertificate;
use Academy\Models\AcademyEnrolment;
use Helpers\DateTimeHelper;
use Link\Link;
use Verify\Verify;

class CertificateService
{
    /**
     * Issue a certificate for an enrolment.
     */
    public function issue(AcademyEnrolment $enrolment): AcademyCertificate
    {
        $existing = AcademyCertificate::where('enrolment_id', $enrolment->id)->first();

        if ($existing) {
            return $existing;
        }

        // Generation logic would go here (PDF generation)
        $certificateNumber = $this->generateNumber($enrolment);

        return AcademyCertificate::create([
            'enrolment_id' => $enrolment->id,
            'certificate_number' => $certificateNumber,
            'status' => CertificateStatus::ISSUED,
            'issued_at' => DateTimeHelper::now(),
        ]);
    }

    public function getSharingUrl(AcademyCertificate $certificate): string
    {
        if (config('academy.integrations.link', true) && class_exists(Link::class)) {
            $link = Link::make()
                ->for($certificate)
                ->validForDays(365) // Certificates usually valid for a long time
                ->view()
                ->create();

            return $link->signedUrl();
        }

        return url(config('academy.urls.certificates') . '/' . $certificate->certificate_number);
    }

    public function revoke(AcademyCertificate $certificate, ?string $otp = null): bool
    {
        if (config('academy.integrations.verify', true) && class_exists(Verify::class)) {
            $user = $certificate->enrolment->user;
            if ($otp) {
                if (!Verify::verify($user->email, $otp)) {
                    throw new AccessDeniedException('Invalid OTP for certificate revocation.');
                }
            } else {
                Verify::send($user->email);

                return false; // Indicating OTP sent, revocation pending
            }
        }

        return $certificate->update(['status' => CertificateStatus::REVOKED]);
    }

    /**
     * Verify a certificate by number.
     */
    public function verify(string $certificateNumber): ?AcademyCertificate
    {
        return AcademyCertificate::where('certificate_number', $certificateNumber)
            ->where('status', CertificateStatus::ISSUED)
            ->first();
    }

    /**
     * Generate a unique certificate number.
     */
    protected function generateNumber(AcademyEnrolment $enrolment): string
    {
        return 'CERT-' . strtoupper(uniqid()) . '-' . $enrolment->id;
    }
}

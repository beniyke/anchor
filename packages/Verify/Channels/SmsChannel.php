<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * SMS Channel
 *
 * Placeholder implementation for SMS delivery
 * Can be extended with actual SMS provider integration (Twilio, Nexmo, etc.)
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Channels;

use Exception;
use Helpers\Data;
use Notify\Notify;
use Verify\Channels\Notifications\SendOtpSmsNotification;
use Verify\Contracts\ChannelInterface;

class SmsChannel implements ChannelInterface
{
    public function send(string $identifier, string $code, ?string $receiverName = null): bool
    {
        try {
            $payload = Data::make([
                'phone' => $identifier,
                'code' => $code
            ]);

            $sent = Notify::sms(SendOtpSmsNotification::class, $payload);

            if ($sent) {
                logger('verify.log')->info('OTP SMS sent successfully', [
                    'to' => $identifier,
                ]);

                return true;
            }

            logger('verify.log')->warning('OTP SMS send returned unsuccessful', [
                'to' => $identifier,
                'result' => $sent,
            ]);

            return false;
        } catch (Exception $e) {
            logger('verify.log')->error('OTP SMS send failed', [
                'to' => $identifier,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

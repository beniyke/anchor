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
use Helpers\Data\Data;
use Helpers\Log;
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
                Log::channel('verify')->info('OTP SMS sent successfully', [
                    'to' => $identifier,
                ]);

                return true;
            }

            Log::channel('verify')->warning('OTP SMS send returned unsuccessful', [
                'to' => $identifier,
                'result' => $sent,
            ]);

            return false;
        } catch (Exception $e) {
            Log::channel('verify')->error('OTP SMS send failed', [
                'to' => $identifier,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

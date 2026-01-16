<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Email Channel
 *
 * Sends OTP codes via email using the Mail system
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Channels;

use Exception;
use Helpers\Data;
use Notify\Notify;
use Verify\Channels\Notifications\SendOtpEmailNotification;
use Verify\Contracts\ChannelInterface;

class EmailChannel implements ChannelInterface
{
    public function send(string $identifier, string $code, ?string $receiverName = null): bool
    {
        try {
            $payload = Data::make([
                'email' => $identifier,
                'name' => $receiverName,
                'code' => $code
            ]);

            $result = Notify::email(SendOtpEmailNotification::class, $payload);
            $sent = ($result['status'] === 'success');

            if ($sent) {
                logger('verify.log')->info('OTP email sent successfully', [
                    'to' => $identifier,
                ]);

                return true;
            }

            logger('verify.log')->warning('OTP email send returned unsuccessful', [
                'to' => $identifier,
                'result' => $result,
            ]);

            return false;
        } catch (Exception $e) {
            logger('verify.log')->error('OTP email send failed', [
                'to' => $identifier,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Audit event types.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Enums;

enum AuditEvent: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case FAILED_LOGIN = 'failed_login';
    case PASSWORD_RESET = 'password_reset';
    case IMPERSONATION_STARTED = 'impersonation_started';
    case IMPERSONATION_STOPPED = 'impersonation_stopped';
    case CUSTOM = 'custom';
}

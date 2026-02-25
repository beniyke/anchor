<?php

declare(strict_types=1);

namespace Academy\Enums;

enum QuestionType: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TRUE_FALSE = 'true_false';
    case SHORT_ANSWER = 'short_answer';
    case LONG_ANSWER = 'long_answer';
    case FILE_UPLOAD = 'file_upload';

    public function label(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Multiple Choice',
            self::TRUE_FALSE => 'True/False',
            self::SHORT_ANSWER => 'Short Answer',
            self::LONG_ANSWER => 'Long Answer',
            self::FILE_UPLOAD => 'File Upload',
        };
    }
}

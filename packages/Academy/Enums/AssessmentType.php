<?php

declare(strict_types=1);

namespace Academy\Enums;

enum AssessmentType: string
{
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case PROJECT = 'project';
    case EXAM = 'exam';

    public function label(): string
    {
        return match ($this) {
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Assignment',
            self::PROJECT => 'Project',
            self::EXAM => 'Exam',
        };
    }
}

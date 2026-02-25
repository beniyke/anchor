# Academy LMS for Anchor

A professional-grade, high-conversion Learning Management System (LMS) package for the Anchor Framework. Every program gets its own clean, optimized landing page.

## Key Features

- **Hierarchical Content**: Organize learning into Programs, Modules, and Lessons.
- **Fluent Content Builder**: Create programs using a developer-friendly API.
- **Advanced Assessment Engine**: Multi-type quizzes with automated grading and feedback.
- **Universal RefIDs**: Secure, public-facing identifiers for all Academy entities.
- **Monetization & Plans**: Flexible one-time payments or instalments via deep `Pay` integration.
- **Interactive Learning**: Integrated live sessions (Zoom/Meet) and community discussions (**Hub**).
- **Gamification**: Reward engagement with automated badges and rank-based achievements.
- **Secure Certification**: Generate and verify professional PDF certificates upon completion.
- **Advanced Analytics & Reporting**: Real-world performance trends and detailed transcripts.

## Installation

```bash
php dock package:install Academy --packages
```

## Quick Start

```php
use Academy\Academy;

// 1. Create a program
$program = Academy::program()
    ->titled('Mastering Anchor Framework')
    ->described('The ultimate guide to building apps with Anchor.')
    ->create();

// 2. Enrol a user
$enrolment = Academy::enrol($user->id, $program->id);

// 3. Complete a lesson
Academy::progress()->complete($enrolment->id, $lesson->id);
```

## CLI & Automation

Academy includes maintenance tools for background operations:

- `academy:verify-cert`: Verify learner credentials.
- `academy:credentials:issue`: Bulk issue certificates.
- `academy:payments:sync`: Sync external payment statuses.
- `academy:prune:expired`: Clean up stale data.

## Documentation

Detailed documentation can be found in [docs/academy.md](../../docs/academy.md).

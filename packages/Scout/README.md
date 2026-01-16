<!-- This file is auto-generated from docs/scout.md -->

# Scout (Recruitment/ATS)

## Overview

Scout is a comprehensive Applicant Tracking System (ATS) for the Anchor Framework. It provides tools for managing job postings, candidate pipelines, interview scheduling, and recruitment analytics.

## Features

- **Job Management**: Create, publish, and track various job types (full-time, remote, etc.).
- **Candidate Pipeline**: Manage candidates through customizable recruitment stages.
- **Interview Scheduling**: Coordinate with the `Slot` package to book interviewers.
- **Offer Engine**: Generate secure offer links via the `Link` package.
- **Unified Analytics**: Track funnel conversion rates and time-to-hire metrics.
- **Recruiter Collaboration**: Private notes and scorecard reviews for hiring teams.

## Facade API

### Job Management

```php
use Scout\Scout;

// Create and publish a job
$job = Scout::job()
    ->title('Senior Backend Engineer')
    ->description('Lead our core API development.')
    ->location('Remote')
    ->publish()
    ->create();
```

### Candidate & Application

```php
// Create a candidate
$candidate = Scout::candidate()
    ->name('John Doe')
    ->email('john@example.com')
    ->talentPool()
    ->create();

// Apply for a job
$application = Scout::application()
    ->for($job)
    ->from($candidate)
    ->submit();
```

### Pipeline Orchestration

```php
// Advance to next stage
Scout::advance($application, $nextStage);

// Schedule an interview
Scout::scheduleInterview($application, $interviewer, [
    'scheduled_at' => '2026-02-01 10:00:00',
    'location' => 'Zoom'
]);

// Reject application
Scout::reject($application, 'Lack of required experience.');
```

### Analytics

```php
$analytics = Scout::analytics();

$funnel = $analytics->funnel($job);
$overview = $analytics->overview();
$avgTime = $analytics->averageTimeToHire();
```

## Integrations

- **Workflow**: Automated hiring pipeline transitions.
- **Slot**: Manage interviewer availability and bookings.
- **Link**: Signed, expiring links for offer letters.
- **Media**: Secure storage for resumes and portfolio documents.
- **Audit**: Comprehensive logs for all recruitment actions.
- **Onboard**: Seamless handoff when a candidate is hired.

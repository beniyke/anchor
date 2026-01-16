<!-- This file is auto-generated from docs/onboard.md -->

# Onboard (Employee Onboarding)

## Overview

Onboard is a core people-management package for the Anchor Framework. It automates employee onboarding through roles-based templates, document verification, training modules, and equipment tracking.

## Features

- **Template Engine**: Create reusable onboarding workflows for different roles (e.g., Engineering, Sales).
- **Task Management**: Structured checklists with required and optional tasks.
- **Document Collection**: Securely collect and verify identity documents and signed contracts.
- **Training Tracking**: Monitor progress through mandatory and elective training modules.
- **Equipment Provisioning**: Track asset requests like laptops and access badges.
- **Analytics**: Real-time progress tracking and completion benchmarks.

## Facade API

### Starting Onboarding

```php
use Onboard\Onboard;

// Start onboarding for a new hire
$onboarding = Onboard::onboarding()
    ->for($user)
    ->using($engineeringTemplate)
    ->dueAt($oneMonthFromNow)
    ->start();
```

### Task & Document Management

```php
// Mark task as complete
Onboard::completeTask($user, $slackSetupTask, 'Setup complete with help from IT.');

// Verify a document
Onboard::verifyDocument($passportUpload, $hrUser);
```

### Training

```php
// Update training progress
Onboard::training()->updateProgress($user, $safetyTraining, 'in_progress');
```

## Integrations

- **Scout**: Automatically initiate onboarding when a candidate is hired.
- **Flow**: (Planned) Sync onboarding tasks with the global task management system.
- **Media**: Secure storage for all uploaded documents.
- **Audit**: Immutable trail of every completion and verification event.
- **Metric**: Automatically trigger goal initialization when onboarding is 100% complete.

## Configuration

Publish the config to `App/Config/onboard.php` to customize:

- Default due dates.
- Manager notification settings.
- Integration triggers.

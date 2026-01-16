<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core service for the Stack (Form Builder) package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Services;

use Audit\Audit;
use Core\Services\ConfigServiceInterface;
use Database\Exceptions\ValidationException;
use Helpers\DateTimeHelper;
use Helpers\Validation\Validator;
use RuntimeException;
use Stack\Models\Event;
use Stack\Models\Form;
use Stack\Models\Submission;
use Stack\Models\SubmissionValue;

class StackManagerService
{
    public function create(array $data): Form
    {
        $form = Form::create($data);

        if (class_exists('Audit\Audit')) {
            Audit::log('stack.form.created', ['title' => $form->title], $form);
        }

        return $form;
    }

    public function submit(Form $form, array $data, ?int $userId = null, ?string $ip = null, ?string $userAgent = null): Submission
    {
        if (!$form->isActive()) {
            throw new RuntimeException("Form '{$form->title}' is not active and cannot accept submissions.");
        }

        $this->validateSubmission($form, $data);

        $submission = Submission::create([
            'refid' => uniqid('sub_', true),
            'stack_form_id' => $form->id,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'metadata' => [
                'submitted_at' => DateTimeHelper::now()->toDateTimeString(),
            ],
        ]);

        foreach ($form->fields as $field) {
            $value = $data[$field->name] ?? null;

            $submissionValue = [
                'stack_submission_id' => $submission->id,
                'stack_field_id' => $field->id,
                'value' => is_array($value) ? json_encode($value) : (string) $value,
            ];

            // Handle Media reference if it's a file upload
            if ($field->isFileUpload() && isset($data[$field->name . '_media_id'])) {
                $submissionValue['media_id'] = (int) $data[$field->name . '_media_id'];
            }

            SubmissionValue::create($submissionValue);
        }

        $this->recordEvent($form, 'submit', $userId, null, ['submission_refid' => $submission->refid]);

        if (class_exists('Audit\Audit')) {
            Audit::log('stack.form.submitted', [
                'form_title' => $form->title,
                'submission_refid' => $submission->refid,
            ], $form);
        }

        return $submission;
    }

    /**
     * Validate submission data against form fields.
     */
    protected function validateSubmission(Form $form, array $data): void
    {
        $rules = [];
        $messages = [];

        $parameters = [];
        foreach ($form->fields as $field) {
            $parameters[$field->name] = $field->label;
            if (!empty($field->field_rules)) {
                $rules[$field->name] = $field->field_rules;
            }
        }

        $validator = (new Validator())->rules($rules)->validate($data);

        if ($validator->has_error()) {
            throw new ValidationException("Submission validation failed.", $validator->errors());
        }
    }

    /**
     * Record an analytics event.
     */
    public function recordEvent(Form $form, string $type, ?int $userId = null, ?string $sessionId = null, array $data = []): Event
    {
        return Event::create([
            'stack_form_id' => $form->id,
            'event_type' => $type,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'data' => $data,
        ]);
    }

    /**
     * Delete a form and its associated data.
     */
    public function delete(Form $form): bool
    {
        $title = $form->title;
        $result = $form->delete();

        if ($result && class_exists('Audit\Audit')) {
            Audit::log('stack.form.deleted', ['title' => $title]);
        }

        return $result;
    }

    /**
     * Cleanup old form submissions and logs.
     */
    public function cleanupSubmissions(): int
    {
        $config = resolve(ConfigServiceInterface::class);
        $days = $config->get('stack.submissions.retention_days', 30);

        $threshold = DateTimeHelper::now()->subDays($days);

        $query = Submission::query()->where('created_at', '<', $threshold);
        $count = $query->count();

        if ($count > 0) {
            $query->delete();

            if (class_exists('Audit\Audit')) {
                Audit::log('stack.submissions.cleaned', [
                    'count' => $count,
                    'retention_days' => $days
                ]);
            }
        }

        return $count;
    }

    /**
     * Get submission data as a flat array of field_name => value.
     */
    public function getSubmissionData(Submission|int $submission): array
    {
        $model = $submission instanceof Submission ? $submission : Submission::find($submission);
        if (!$model) {
            return [];
        }

        return $model->values->pluck('value', 'field.name');
    }
}

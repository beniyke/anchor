<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent form builder for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Services\Builders;

use RuntimeException;
use Stack\Models\Field;
use Stack\Models\FieldOption;
use Stack\Models\Form;
use Stack\Services\StackManagerService;

class FormBuilder
{
    private ?string $title = null;

    private ?string $slug = null;

    private ?string $description = null;

    private string $status = 'draft';

    private array $settings = [];

    private array $fields = [];

    private ?int $createdBy = null;

    public function __construct(
        private readonly StackManagerService $manager
    ) {
    }

    public function title(string $title): self
    {
        $this->title = $title;
        if (is_null($this->slug)) {
            $this->slug = strtolower(str_replace(' ', '-', $title));
        }

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function active(): self
    {
        return $this->status('active');
    }

    public function setting(string $key, mixed $value): self
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function by(int $userId): self
    {
        $this->createdBy = $userId;

        return $this;
    }

    /**
     * Start building a field.
     */
    public function withField(string $name, string $label): FieldBuilder
    {
        $builder = new FieldBuilder($this);

        return $builder->name($name)->label($label);
    }

    public function addField(array $definition): self
    {
        $this->fields[] = $definition;

        return $this;
    }

    /**
     * Create the form and its fields.
     */
    public function create(): Form
    {
        if (is_null($this->title)) {
            throw new RuntimeException('Form title is required.');
        }

        $form = $this->manager->create([
            'refid' => uniqid('frm_', true),
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'settings' => $this->settings,
            'created_by' => $this->createdBy,
        ]);

        foreach ($this->fields as $index => $fieldData) {
            $options = $fieldData['options'] ?? [];
            unset($fieldData['options']);

            $fieldData['stack_form_id'] = $form->id;
            $fieldData['order_index'] = $fieldData['order_index'] ?: $index;

            $field = Field::create($fieldData);

            foreach ($options as $optionData) {
                $optionData['stack_field_id'] = $field->id;
                FieldOption::create($optionData);
            }
        }

        return $form;
    }
}

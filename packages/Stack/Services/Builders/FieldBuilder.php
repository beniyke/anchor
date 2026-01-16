<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent field builder for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Services\Builders;

use Stack\Models\Field;

class FieldBuilder
{
    private string $name;

    private string $label;

    private string $type = 'text';

    private ?string $placeholder = null;

    private array $rules = [];

    private int $orderIndex = 0;

    private array $settings = [];

    private array $options = [];

    public function __construct(
        private readonly FormBuilder $formBuilder
    ) {
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function rules(array|string $rules): self
    {
        if (is_string($rules)) {
            $parts = explode('|', $rules);
            foreach ($parts as $part) {
                $subParts = explode(':', $part, 2);
                $name = $subParts[0];
                $value = $subParts[1] ?? true;
                $this->rules[$name] = $value;
            }
        } else {
            $this->rules = $rules;
        }

        return $this;
    }

    public function required(): self
    {
        $this->rules['required'] = true;

        return $this;
    }

    public function order(int $index): self
    {
        $this->orderIndex = $index;

        return $this;
    }

    public function setting(string $key, mixed $value): self
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function option(string $label, string $value): self
    {
        $this->options[] = [
            'label' => $label,
            'value' => $value,
            'order_index' => count($this->options),
        ];

        return $this;
    }

    /**
     * Finish building field and return to form builder.
     */
    public function add(): FormBuilder
    {
        return $this->formBuilder->addField($this->getDefinition());
    }

    public function getDefinition(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'field_rules' => $this->rules,
            'order_index' => $this->orderIndex,
            'settings' => $this->settings,
            'options' => $this->options,
        ];
    }
}

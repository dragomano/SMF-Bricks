<?php declare(strict_types=1);

namespace Tests\Unit\Forms;

use Bugo\Bricks\Forms\Interfaces\FieldInterface;

use function array_merge;

/**
 * A user-land implementation of FieldInterface (not extending Bugo\Bricks\Forms\Field).
 * The renderer must accept it: this is what the interfaces promise.
 */
class CustomField implements FieldInterface
{
	private array $attributes = [
		'name'  => 'custom_field',
		'label' => 'Custom field',
		'type'  => 'text',
		'id'    => 'custom_field',
	];

	public static function make(string $name, string $label): static
	{
		return new static();
	}

	public function setId(string $id): static
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	public function setType(string $type): static
	{
		$this->attributes['type'] = $type;

		return $this;
	}

	public function setValue(mixed $value): static
	{
		$this->attributes['value'] = $value;

		return $this;
	}

	public function required(bool $required = true): static
	{
		$this->attributes['required'] = $required;

		return $this;
	}

	public function readonly(bool $readonly = true): static
	{
		$this->attributes['readonly'] = $readonly;

		return $this;
	}

	public function disabled(bool $disabled = true): static
	{
		$this->attributes['disabled'] = $disabled;

		return $this;
	}

	public function setAttributes(array $attributes): static
	{
		$this->attributes = array_merge($this->attributes, $attributes);

		return $this;
	}

	public function setClass(string $class): static
	{
		$this->attributes['class'] = $class;

		return $this;
	}

	public function setStyle(string $style): static
	{
		$this->attributes['style'] = $style;

		return $this;
	}

	public function getName(): string
	{
		return $this->attributes['name'];
	}

	public function getValue(): mixed
	{
		return $this->attributes['value'] ?? null;
	}

	public function getType(): string
	{
		return $this->attributes['type'];
	}

	public function toArray(): array
	{
		return $this->attributes;
	}
}

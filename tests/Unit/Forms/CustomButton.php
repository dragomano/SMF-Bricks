<?php declare(strict_types=1);

namespace Tests\Unit\Forms;

use Bugo\Bricks\Forms\Interfaces\ButtonInterface;

use function array_merge;

/**
 * A user-land implementation of ButtonInterface (not extending Bugo\Bricks\Forms\Button).
 */
class CustomButton implements ButtonInterface
{
	private array $attributes = [
		'name'  => 'custom_button',
		'value' => 'Press me',
		'type'  => 'button',
	];

	public static function make(string $name, string $value): static
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

	public function getValue(): string
	{
		return (string) $this->attributes['value'];
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

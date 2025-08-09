<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\ButtonInterface;
use Bugo\Bricks\HasShortMethods;

use function array_merge;

/**
 * @method self id(string $id)
 * @method self type(string $type)
 * @method self value(mixed $value)
 * @method self class(string $class)
 * @method self style(string $style)
 */
class Button implements ButtonInterface
{
	use HasShortMethods;

	protected array $attributes = [];

	protected function __construct(string $name, string $value)
	{
		$this->attributes['name'] = $name;

		$this->setValue($value);
		$this->setType(HtmlFieldType::BUTTON->value);
	}

	public static function make(string $name, string $value): static
	{
		return new static($name, $value);
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

	public function setAttributes(array $attributes): static
	{
		$this->attributes = array_merge($this->attributes, $attributes);

		return $this;
	}

	public function getName(): string
	{
		return $this->attributes['name'];
	}

	public function getValue(): string
	{
		return $this->attributes['value'];
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

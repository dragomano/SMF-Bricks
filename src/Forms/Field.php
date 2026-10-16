<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\FieldInterface;
use Bugo\Bricks\HasShortMethods;
use InvalidArgumentException;

use function array_merge;
use function is_string;

/**
 * @method self id(string $id)
 * @method self type(string $type)
 * @method self value(mixed $value)
 * @method self class(string $class)
 * @method self style(string $style)
 *
 * @phpstan-consistent-constructor
 */
class Field implements FieldInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
	protected array $attributes = [];

	protected function __construct(string $name, string $label)
	{
		$this->attributes['name'] = $name;
		$this->attributes['label'] = $label;

		$this->setId($name);
	}

	public static function make(string $name, string $label): static
	{
		return new static($name, $label);
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

	/**
	 * @param array<string, mixed> $attributes
	 */
	public function setAttributes(array $attributes): static
	{
		$this->attributes = array_merge($this->attributes, $attributes);

		return $this;
	}

	public function getName(): string
	{
		$name = $this->attributes['name'];

		if (! is_string($name)) {
			throw new InvalidArgumentException('The field name must be a string');
		}

		return $name;
	}

	public function getValue(): mixed
	{
		return $this->attributes['value'];
	}

	public function getType(): string
	{
		$type = $this->attributes['type'] ?? null;

		if (! is_string($type)) {
			throw new InvalidArgumentException('The field type must be a string');
		}

		return $type;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return $this->attributes;
	}
}

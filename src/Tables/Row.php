<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\HasShortMethods;
use Bugo\Bricks\Tables\Interfaces\RowInterface;

use function is_string;

/**
 * @method self value(mixed $value)
 * @method self position(mixed $value)
 * @method self class(string $class)
 * @method self style(string $class)
 */
class Row implements RowInterface
{
	use HasShortMethods;

	private array $attributes = [];

	private function __construct(string $value, string $class = '')
	{
		$this->setValue($value);
		$this->setClass($class);
		$this->setPosition(RowPosition::BELOW_TABLE_DATA);
	}

	public static function make(string $value = '', string $class = ''): static
	{
		return new static($value, $class);
	}

	public function setValue(string $value): static
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

	public function setPosition(RowPosition|string $position): static
	{
		$this->attributes['position'] = is_string($position) ? $position : $position->name();

		return $this;
	}

	public function toArray(): array
	{
		return $this->attributes;
	}
}

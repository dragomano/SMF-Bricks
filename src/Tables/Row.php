<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\HasShortMethods;
use Bugo\Bricks\Tables\Interfaces\RowInterface;
use InvalidArgumentException;

use function implode;
use function in_array;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * @method self value(mixed $value)
 * @method self position(mixed $value)
 * @method self customPosition(string $position)
 * @method self class(string $class)
 * @method self style(string $class)
 *
 * @phpstan-consistent-constructor
 */
class Row implements RowInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
	protected array $attributes = [];

	protected function __construct(string $value, string $class = '')
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
		$name = is_string($position) ? strtolower($position) : $position->name();

		if (! in_array($name, RowPosition::names(), true)) {
			throw new InvalidArgumentException(sprintf(
				'Unknown row position "%s", expected one of: %s',
				$name,
				implode(', ', RowPosition::names())
			));
		}

		$this->attributes['position'] = $name;

		return $this;
	}

	/**
	 * Sets a position provided by a custom renderer without SMF position validation.
	 */
	public function setCustomPosition(string $position): static
	{
		$this->attributes['position'] = $position;

		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return $this->attributes;
	}
}

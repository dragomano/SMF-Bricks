<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\Interfaces\SizeableInterface;
use Bugo\Bricks\Forms\Traits\HasMultipleAttribute;
use Bugo\Bricks\Forms\Traits\HasOptions;
use Bugo\Bricks\Forms\Traits\HasSelectedAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;
use InvalidArgumentException;
use Stringable;

use function implode;
use function is_array;
use function is_scalar;

class SelectField extends Field implements OptionProviderInterface, SelectableInterface, SizeableInterface
{
	use HasMultipleAttribute {
		multiple as private setMultipleFlag;
	}
	use HasOptions;
	use HasSelectedAttribute;
	use HasSizeAttribute;

	private mixed $rawValue = null;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::SELECT->value);
	}

	public function setValue(mixed $value): static
	{
		$this->rawValue = $value;

		$normalized = $this->normalizeValue($value);

		$this->selected($normalized);

		return parent::setValue($normalized);
	}

	/**
	 * The flag is applied before the value is normalized again,
	 * so that the call order of multiple() and setValue() does not matter.
	 */
	public function multiple(bool $multiple = true): static
	{
		$this->setMultipleFlag($multiple);

		if ($this->rawValue !== null) {
			$this->setValue($this->rawValue);
		}

		return $this;
	}

	/**
	 * @return array<int|string, mixed>|string
	 */
	private function normalizeValue(mixed $value): string|array
	{
		if (($this->attributes['multiple'] ?? false) !== true) {
			$values = is_array($value) ? $value : [$value];
			$parts  = [];

			foreach ($values as $part) {
				if ($part !== null && ! is_scalar($part) && ! $part instanceof Stringable) {
					throw new InvalidArgumentException('Select values must be scalars, stringable objects or null');
				}

				$parts[] = (string) $part;
			}

			return implode(',', $parts);
		}

		return (array) $value;
	}
}

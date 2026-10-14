<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\Interfaces\SizeableInterface;
use Bugo\Bricks\Forms\Traits\HasMultipleAttribute;
use Bugo\Bricks\Forms\Traits\HasOptions;
use Bugo\Bricks\Forms\Traits\HasSelectedAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

use function implode;
use function is_array;

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

	private function normalizeValue(mixed $value): string|array
	{
		if (empty($this->attributes['multiple'])) {
			return is_array($value) ? implode(',', $value) : (string) $value;
		}

		return (array) $value;
	}
}

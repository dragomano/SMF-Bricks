<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionableInterface;
use Bugo\Bricks\Forms\Interfaces\SizeableInterface;
use Bugo\Bricks\Forms\Traits\HasMultipleAttribute;
use Bugo\Bricks\Forms\Traits\HasOptions;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

class SelectField extends Field implements OptionableInterface, SizeableInterface
{
	use HasMultipleAttribute;
	use HasOptions;
	use HasSizeAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::SELECT->value);
	}

	public function setValue(mixed $value): static
	{
		if (! empty($this->attributes['multiple'])) {
			$value = (array) $value;
		}

		return parent::setValue($value);
	}
}

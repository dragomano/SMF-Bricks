<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasMinMaxStepAttributes;

class RangeField extends NumberField
{
	use HasMinMaxStepAttributes;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::RANGE->value);
	}
}

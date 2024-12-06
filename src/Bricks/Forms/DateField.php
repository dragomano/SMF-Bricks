<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\MinMaxStepInterface;
use Bugo\Bricks\Forms\Traits\HasMinMaxStepAttributes;
use Bugo\Bricks\Forms\Traits\HasPatternAttribute;

class DateField extends NumberField implements MinMaxStepInterface
{
	use HasMinMaxStepAttributes;
	use HasPatternAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::DATE->value);
	}
}

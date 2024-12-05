<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\StepableInterface;
use Bugo\Bricks\Forms\Traits\HasMinMaxStepAttributes;
use Bugo\Bricks\Forms\Traits\HasPlaceholderAttribute;

class NumberField extends InputField implements StepableInterface
{
	use HasPlaceholderAttribute;
	use HasMinMaxStepAttributes;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::NUMBER->value);
	}
}

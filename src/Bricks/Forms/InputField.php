<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasMinMaxLengthAttribute;

abstract class InputField extends Field
{
	use HasMinMaxLengthAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType('input');
	}
}

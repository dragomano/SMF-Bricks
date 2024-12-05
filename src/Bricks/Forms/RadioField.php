<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionableInterface;
use Bugo\Bricks\Forms\Traits\HasOptions;

class RadioField extends InputField implements OptionableInterface
{
	use HasOptions;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::RADIO->value);
	}
}

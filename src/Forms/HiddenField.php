<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

class HiddenField extends InputField
{
	protected function __construct(string $name, string $label = '')
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::HIDDEN->value);
	}

	public static function make(string $name, string $label = ''): static
	{
		return parent::make($name, $label);
	}
}

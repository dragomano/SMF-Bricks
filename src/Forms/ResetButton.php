<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

class ResetButton extends Button
{
	protected function __construct(string $name, string $value)
	{
		parent::__construct($name, $value);

		$this->setType(HtmlFieldType::RESET->value);
	}

	public static function make(string $name = 'reset', string $value = 'Reset'): static
	{
		return new static($name, $value);
	}
}

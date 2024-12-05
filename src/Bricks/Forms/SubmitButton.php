<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

class SubmitButton extends Button
{
	protected function __construct(string $name, string $value)
	{
		parent::__construct($name, $value);

		$this->setType(HtmlFieldType::SUBMIT->value);
	}

	public static function make(string $name = 'submit', string $value = 'Save'): static
	{
		return new static($name, $value);
	}
}

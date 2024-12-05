<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

class CheckboxField extends InputField
{
	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::CHECKBOX->value);
	}

	public function checked(bool $checked = true): static
	{
		$this->attributes['checked'] = $checked;

		return $this;
	}
}

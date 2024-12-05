<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use function filter_var;

class ColorField extends InputField
{
	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType('color');
	}

	public function setValue(mixed $value): static
	{
		$value = (string) filter_var($value, FILTER_VALIDATE_REGEXP, [
			"options" => [
				"regexp" => '/^#[0-9A-Fa-f]{6}$/'
			]
		]);

		$this->attributes['value'] = $value;

		return $this;
	}
}

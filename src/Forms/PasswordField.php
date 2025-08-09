<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasMinMaxLengthAttribute;
use Bugo\Bricks\Forms\Traits\HasPatternAttribute;
use Bugo\Bricks\Forms\Traits\HasPlaceholderAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

class PasswordField extends InputField
{
	use HasMinMaxLengthAttribute;
	use HasPatternAttribute;
	use HasPlaceholderAttribute;
	use HasSizeAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::PASSWORD->value);
	}

	public function setAutocomplete(string $autocomplete): static
	{
		$this->attributes['autocomplete'] = $autocomplete;

		return $this;
	}

	public function setInputMode(string $mode): static
	{
		$this->attributes['input' . 'mode'] = $mode;

		return $this;
	}
}

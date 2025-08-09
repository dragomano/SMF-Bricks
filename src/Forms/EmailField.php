<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasPatternAttribute;
use Bugo\Bricks\Forms\Traits\HasPlaceholderAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

class EmailField extends InputField
{
	use HasPatternAttribute;
	use HasPlaceholderAttribute;
	use HasSizeAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::EMAIL->value);
	}

	public static function make(string $name = 'email', string $label = 'Email'): static
	{
		return parent::make($name, $label);
	}
}

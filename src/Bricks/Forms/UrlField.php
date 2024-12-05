<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasPatternAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

class UrlField extends InputField
{
	use HasPatternAttribute;
	use HasSizeAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::URL->value);
	}
}
<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasMinMaxLengthAttribute;
use Bugo\Bricks\Forms\Traits\HasPlaceholderAttribute;

class TextareaField extends Field
{
	use HasMinMaxLengthAttribute;
	use HasPlaceholderAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::TEXTAREA->value);
	}

	public function setCols(int $cols): static
	{
		$this->attributes['cols'] = $cols;

		return $this;
	}

	public function setRows(int $rows): static
	{
		$this->attributes['rows'] = $rows;

		return $this;
	}
}

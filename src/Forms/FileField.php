<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Traits\HasMultipleAttribute;

class FileField extends InputField
{
	use HasMultipleAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::FILE->value);
	}

	public function accept(string $mimeTypes): static
	{
		$this->attributes['accept'] = $mimeTypes;

		return $this;
	}
}

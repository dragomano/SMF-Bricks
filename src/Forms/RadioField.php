<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\Traits\HasOptions;
use Bugo\Bricks\Forms\Traits\HasSelectedAttribute;

class RadioField extends InputField implements OptionProviderInterface, SelectableInterface
{
	use HasOptions;
	use HasSelectedAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::RADIO->value);
	}

	public function setValue(mixed $value): static
	{
		$this->selected($value);

		return parent::setValue($value);
	}
}

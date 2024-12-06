<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\Interfaces\SizeableInterface;
use Bugo\Bricks\Forms\Traits\HasMultipleAttribute;
use Bugo\Bricks\Forms\Traits\HasOptions;
use Bugo\Bricks\Forms\Traits\HasSelectedAttribute;
use Bugo\Bricks\Forms\Traits\HasSizeAttribute;

use function implode;
use function is_array;

class SelectField extends Field implements OptionProviderInterface, SelectableInterface, SizeableInterface
{
	use HasMultipleAttribute;
	use HasOptions;
	use HasSelectedAttribute;
	use HasSizeAttribute;

	protected function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);

		$this->setType(HtmlFieldType::SELECT->value);
	}

	public function setValue(mixed $value): static
	{
		if (empty($this->attributes['multiple'])) {
			$value = is_array($value) ? implode(',', $value) : (string) $value;
		} else {
			$value = (array) $value;
		}

		$this->selected($value);

		return parent::setValue($value);
	}
}

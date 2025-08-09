<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasMultipleAttribute
{
	public function multiple(bool $multiple = true): static
	{
		$this->attributes['multiple'] = $multiple;

		return $this;
	}
}

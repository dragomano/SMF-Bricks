<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasSelectedAttribute
{
	public function selected(mixed $selected): static
	{
		$this->attributes['selected'] = $selected;

		return $this;
	}
}

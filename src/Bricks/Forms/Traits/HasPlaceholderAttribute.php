<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasPlaceholderAttribute
{
	public function setPlaceholder(string $text): static
	{
		$this->attributes['placeholder'] = $text;

		return $this;
	}
}

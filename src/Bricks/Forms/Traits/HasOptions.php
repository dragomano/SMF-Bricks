<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasOptions
{
	public function setOptions(array $options): static
	{
		$this->attributes['options'] = $options;

		return $this;
	}

	public function getOptions(): array
	{
		return $this->attributes['options'];
	}
}

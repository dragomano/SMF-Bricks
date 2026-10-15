<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasOptions
{
	/**
	 * @param array<int|string, mixed> $options
	 */
	public function setOptions(array $options): static
	{
		$this->attributes['options'] = $options;

		return $this;
	}

	/**
	 * @return array<int|string, mixed>
	 */
	public function getOptions(): array
	{
		return $this->attributes['options'];
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

use InvalidArgumentException;

use function is_array;

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
		$options = $this->attributes['options'] ?? [];

		if (! is_array($options)) {
			throw new InvalidArgumentException('The field options must be an array');
		}

		return $options;
	}
}

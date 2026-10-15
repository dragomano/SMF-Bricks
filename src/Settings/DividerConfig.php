<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

/**
 * @phpstan-consistent-constructor
 */
class DividerConfig extends AbstractConfig
{
	public static function make(string $name = ''): static
	{
		return new static($name);
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasPatternAttribute
{
	public function setPattern(string $pattern): static
	{
		$this->attributes['pattern'] = $pattern;

		return $this;
	}
}

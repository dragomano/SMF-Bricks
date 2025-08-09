<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasSizeAttribute
{
	public function setSize(int $size): static
	{
		$this->attributes['size'] = $size;

		return $this;
	}
}

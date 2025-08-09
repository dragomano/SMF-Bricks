<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasMinMaxLengthAttribute
{
	public function setMinLength(int $length): static
	{
		$this->attributes['minlength'] = $length;

		return $this;
	}

	public function setMaxLength(int $length): static
	{
		$this->attributes['maxlength'] = $length;

		return $this;
	}
}

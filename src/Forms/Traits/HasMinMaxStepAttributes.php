<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Traits;

trait HasMinMaxStepAttributes
{
	public function setMin(float|int $value = 0): static
	{
		$this->attributes['min'] = $value;

		return $this;
	}

	public function setMax(float|int $value = 10): static
	{
		$this->attributes['max'] = $value;

		return $this;
	}

	public function setStep(float|int $value = 20): static
	{
		$this->attributes['step'] = $value;

		return $this;
	}
}

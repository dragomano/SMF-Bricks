<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class FloatConfig extends AbstractConfig
{
	protected string $type = 'float';

	public function setMin(float $min): self
	{
		$this->params['min'] = $min;

		return $this;
	}

	public function setMax(float $max): self
	{
		$this->params['max'] = $max;

		return $this;
	}

	public function setStep(float $step): self
	{
		$this->params['step'] = $step;

		return $this;
	}

	public function extendData(): array
	{
		return [
			'min'  => $this->params['min'] ?? null,
			'max'  => $this->params['max'] ?? null,
			'step' => $this->params['step'] ?? 0.1,
		];
	}
}

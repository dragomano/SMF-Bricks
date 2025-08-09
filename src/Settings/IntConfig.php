<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class IntConfig extends AbstractConfig
{
	protected string $type = 'int';

	public function setMin(int $min): self
	{
		$this->params['min'] = $min;

		return $this;
	}

	public function setMax(int $max): self
	{
		$this->params['max'] = $max;

		return $this;
	}

	public function setStep(int $step): self
	{
		$this->params['step'] = $step;

		return $this;
	}

	public function extendData(): array
	{
		return [
			'min'  => $this->params['min'] ?? null,
			'max'  => $this->params['max'] ?? null,
			'step' => $this->params['step'] ?? 1,
		];
	}
}

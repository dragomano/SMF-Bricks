<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface StepableInterface
{
	public function setMin(float|int $value = 0): static;

	public function setMax(float|int $value = 10): static;

	public function setStep(float|int $value = 20): static;
}

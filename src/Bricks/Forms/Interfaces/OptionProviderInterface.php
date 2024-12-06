<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface OptionProviderInterface
{
	public function setOptions(array $options): static;

	public function getOptions(): array;
}

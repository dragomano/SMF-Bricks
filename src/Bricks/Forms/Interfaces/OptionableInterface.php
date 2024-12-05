<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface OptionableInterface
{
	public function setOptions(array $options): static;

	public function getOptions(): array;
}

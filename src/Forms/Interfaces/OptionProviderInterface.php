<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface OptionProviderInterface
{
	/**
	 * @param array<int|string, mixed> $options
	 */
	public function setOptions(array $options): static;

	/**
	 * @return array<int|string, mixed>
	 */
	public function getOptions(): array;
}

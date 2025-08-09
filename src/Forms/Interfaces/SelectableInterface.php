<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface SelectableInterface
{
	public function selected(string $key): static;
}

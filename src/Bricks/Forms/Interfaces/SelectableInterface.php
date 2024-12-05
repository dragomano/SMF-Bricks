<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface SelectableInterface
{
	public function setSelected(string $key): static;
}

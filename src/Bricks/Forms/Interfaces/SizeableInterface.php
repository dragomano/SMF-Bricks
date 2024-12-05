<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

interface SizeableInterface
{
	public function setSize(int $size): static;
}

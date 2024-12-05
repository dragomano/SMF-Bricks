<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface BrickInterface
{
	public function setClass(string $class): static;

	public function setStyle(string $style): static;

	public function toArray(): array;
}

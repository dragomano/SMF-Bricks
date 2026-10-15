<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface RendererInterface
{
	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): void;
}

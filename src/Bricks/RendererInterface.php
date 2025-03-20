<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface RendererInterface
{
	public function render(array $data): void;
}

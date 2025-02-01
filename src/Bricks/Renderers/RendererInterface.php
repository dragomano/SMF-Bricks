<?php declare(strict_types=1);

namespace Bugo\Bricks\Renderers;

interface RendererInterface
{
	public function render(array $data): void;
}

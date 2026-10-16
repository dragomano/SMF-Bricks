<?php declare(strict_types=1);

namespace Bugo\Bricks;

/**
 * A renderer that returns the rendered markup instead of echoing it.
 *
 * The original RendererInterface is intentionally left untouched: it is implemented
 * by third-party renderers (Light Portal among them), so this is a separate contract.
 */
interface StringRendererInterface
{
	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): string;
}

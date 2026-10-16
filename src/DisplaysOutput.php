<?php declare(strict_types=1);

namespace Bugo\Bricks;

/**
 * Shared presenter behaviour: a string renderer returns the markup (and the presenter
 * echoes it), while the classic RendererInterface writes to the output by itself.
 */
trait DisplaysOutput
{
	/**
	 * @param array<int|string, mixed> $data
	 */
	private function display(array $data): void
	{
		if ($this->renderer instanceof StringRendererInterface) {
			echo $this->renderer->render($data);

			return;
		}

		$this->renderer->render($data);
	}
}

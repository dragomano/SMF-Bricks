<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

/**
 * @phpstan-consistent-constructor
 */
class CallbackConfig extends AbstractConfig
{
	protected string $type = 'callback';

	public function setCallback(callable $callback): self
	{
		$this->params['callback'] = $callback;

		return $this;
	}

	/**
	 * @return array<int|string, mixed>
	 */
	public function extendData(): array
	{
		return [
			'callback' => $this->params['callback'] ?? null,
		];
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class CallbackConfig extends AbstractConfig
{
	protected string $type = 'callback';

	public function setCallback(callable $callback): self
	{
		$this->params['callback'] = $callback;

		return $this;
	}

	public function extendData(): array
	{
		return [
			'callback' => $this->params['callback'] ?? null,
		];
	}
}

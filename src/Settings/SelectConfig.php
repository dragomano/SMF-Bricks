<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class SelectConfig extends AbstractConfig
{
	protected string $type = 'select';

	/**
	 * @param array<int|string, mixed> $options
	 */
	public function setOptions(array $options): self
	{
		$this->params['options'] = $options;

		return $this;
	}

	/**
	 * @return array<int|string, mixed>
	 */
	public function extendData(): array
	{
		return [
			2 => $this->params['options'] ?? [],
		];
	}
}

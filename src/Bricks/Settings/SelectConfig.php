<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class SelectConfig extends AbstractConfig
{
	protected string $type = 'select';

	public function setOptions(array $options): self
	{
		$this->params['options'] = $options;

		return $this;
	}

	public function extendData(): array
	{
		return [
			2 => $this->params['options'],
		];
	}
}

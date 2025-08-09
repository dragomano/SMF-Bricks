<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

class TextConfig extends AbstractConfig
{
	protected string $type = 'text';

	public function setPlaceholder(string $placeholder): self
	{
		$this->params['placeholder'] = $placeholder;

		return $this;
	}

	public function setSize(string $size): self
	{
		$this->params['size'] = $size;

		return $this;
	}

	public function extendData(): array
	{
		return [
			'placeholder' => $this->params['placeholder'] ?? null,
			'size'        => $this->params['size'] ?? null,
		];
	}
}

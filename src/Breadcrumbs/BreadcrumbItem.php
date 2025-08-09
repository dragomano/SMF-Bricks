<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use function array_filter;

class BreadcrumbItem
{
	protected function __construct(
		private readonly string $name,
		private readonly ?string $url = null,
		private ?string $before = null,
		private ?string $after = null
	) {}

	public static function make(
        string $name,
        ?string $url = null,
        ?string $before = null,
        ?string $after = null
    ): static
	{
		return new static($name, $url, $before, $after);
	}

	public function setBefore(?string $before): self
	{
		$this->before = $before;

		return $this;
	}

	public function setAfter(?string $after): self
	{
		$this->after = $after;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function getBefore(): ?string
	{
		return $this->before;
	}

	public function getAfter(): ?string
	{
		return $this->after;
	}

	public function toArray(): array
	{
		return array_filter([
			'name'   => $this->getName(),
			'url'    => $this->getUrl(),
			'before' => $this->getBefore(),
			'after'  => $this->getAfter(),
		]);
	}
}

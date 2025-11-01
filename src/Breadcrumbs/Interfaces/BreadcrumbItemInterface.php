<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs\Interfaces;

interface BreadcrumbItemInterface
{
	public static function make(
		string $name,
		?string $url = null,
		?string $before = null,
		?string $after = null
	): static;

	public function setBefore(?string $before): self;

	public function setAfter(?string $after): self;

	public function getName(): string;

	public function getUrl(): ?string;

	public function getBefore(): ?string;

	public function getAfter(): ?string;

	public function toArray(): array;
}
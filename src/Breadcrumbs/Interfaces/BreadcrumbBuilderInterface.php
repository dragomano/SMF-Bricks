<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs\Interfaces;

interface BreadcrumbBuilderInterface
{
	public static function make(): static;

	public function add(string $name, ?string $url = null, ?string $before = null, ?string $after = null): self;

	public function addItem(BreadcrumbItemInterface $item): self;

	/**
	 * @param array<BreadcrumbItemInterface> $items
	 */
	public function addItems(array $items): self;

	public function update(int $index, string $key, mixed $value): self;

	public function remove(int $index): self;

	/**
	 * @return array<int, BreadcrumbItemInterface>
	 */
	public function getAll(): array;

	/**
	 * @return array<string, mixed>|null
	 */
	public function getByIndex(int $index): ?array;

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function build(): array;
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs\Interfaces;

use Bugo\Bricks\Breadcrumbs\BreadcrumbItem;

interface BreadcrumbBuilderInterface
{
	public static function make(): static;

	public function add(string $name, ?string $url = null, ?string $before = null, ?string $after = null): self;

	public function addItem(BreadcrumbItem $item): self;

	public function addItems(array $items): self;

	public function update(int $index, string $key, mixed $value): self;

	public function remove(int $index): self;

	public function getAll(): array;

	public function getByIndex(int $index): ?array;

	public function build(): array;
}

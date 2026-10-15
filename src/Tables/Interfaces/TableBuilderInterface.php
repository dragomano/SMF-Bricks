<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables\Interfaces;

use Bugo\Bricks\BuilderInterface;
use Bugo\Bricks\Tables\Column;
use Bugo\Bricks\Tables\Row;

interface TableBuilderInterface extends BuilderInterface
{
	public function paginate(int $itemsPerPage = 15): static;

	public function setNoItemsLabel(string $label): static;

	public function setFormAction(string $action): static;

	public function setDefaultSortColumn(string $column): static;

	public function withParams(
		?int $perPage = null,
		?string $noItemsLabel = null,
		?string $action = null,
		?string $defaultSortColumn = null
	): static;

	/**
	 * @param array<int|string, mixed>|callable $items
	 * @param array<string, mixed> $params
	 */
	public function setItems(array|callable $items, array $params = []): static;

	/**
	 * @param array<string, mixed> $params
	 */
	public function setCount(mixed $count, array $params = []): static;

	/**
	 * @param array<ColumnInterface> $columns
	 */
	public function addColumns(array $columns, ?string $class = null, ?string $style = null): static;

	public function addColumn(Column $column): static;

	public function removeColumn(string $name): static;

	/**
	 * @param array<string, mixed> $data
	 */
	public function addFormData(array $data): static;

	/**
	 * @param array<string, mixed> $fields
	 */
	public function addHiddenFields(array $fields): static;

	public function setScript(string $script): static;

	public function removeScript(): static;

	/**
	 * @param array<RowInterface> $rows
	 */
	public function addRows(array $rows, ?string $class = null, ?string $style = null): static;

	public function addRow(Row $row, ?string $class = null): static;
}

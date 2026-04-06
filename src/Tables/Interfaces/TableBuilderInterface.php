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

	public function setItems(array|callable $items, array $params = []): static;

	public function setCount(mixed $count, array $params = []): static;

	public function addColumns(array $columns, ?string $class = null, ?string $style = null): static;

	public function addColumn(Column $column): static;

	public function removeColumn(string $name): static;

	public function addFormData(array $data): static;

	public function addHiddenFields(array $fields): static;

	public function setScript(string $script): static;

	public function removeScript(): static;

	public function addRows(array $rows, ?string $class = null, ?string $style = null): static;

	public function addRow(Row $row, ?string $class = null): static;
}

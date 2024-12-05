<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables\Interfaces;

use Bugo\Bricks\BuilderInterface;
use Bugo\Bricks\Tables\Column;
use Bugo\Bricks\Tables\Row;

interface TableBuilderInterface extends BuilderInterface
{
	public function paginate(int $itemsPerPage = 15): self;

	public function setNoItemsLabel(string $label): self;

	public function setFormAction(string $action): self;

	public function setDefaultSortColumn(string $column): self;

	public function withParams(
		?int $perPage = null,
		?string $noItemsLabel = null,
		?string $action = null,
		?string $defaultSortColumn = null
	): self;

	public function setItems(array|callable $items, array $params = []): self;

	public function setCount(mixed $count, array $params = []): self;

	public function addColumns(array $columns, ?string $class = null, ?string $style = null): self;

	public function addColumn(Column $column): self;

	public function removeColumn(string $name): self;

	public function addFormData(array $data): self;

	public function addHiddenFields(array $fields): self;

	public function setScript(string $script): self;

	public function removeScript(): self;

	public function addRows(array $rows, ?string $class = null, ?string $style = null): self;

	public function addRow(Row $row, ?string $class = null): self;
}

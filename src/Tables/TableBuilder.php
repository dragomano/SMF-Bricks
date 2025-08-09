<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\HasShortMethods;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use InvalidArgumentException;

use function array_merge;
use function filter_var;
use function get_debug_type;
use function is_array;
use function is_callable;
use function sprintf;

/**
 * @method self id(string $id)
 * @method self name(string $name)
 * @method self title(string $title)
 * @method self items(array|callable $items, array $params = [])
 * @method self count(mixed $count, array $params = [])
 * @method self script(string $script)
 */
class TableBuilder implements TableBuilderInterface
{
	use HasShortMethods;

	private array $attributes = [];

	protected function __construct(string $id, string $title)
	{
		$this->setId($id);
		$this->setTitle($title);
		$this->paginate();
	}

	public static function make(string $id, string $title): static
	{
		return new static($id, $title);
	}

	public function setId(string $id): self
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	public function setName(string $name): self
	{
		$this->attributes['form']['name'] = $name;

		return $this;
	}

	public function setTitle(string $title): self
	{
		$this->attributes['title'] = $title;

		return $this;
	}

	public function paginate(int $itemsPerPage = 15): self
	{
		$this->attributes['items_per_page'] = $itemsPerPage;

		return $this;
	}

	public function setNoItemsLabel(string $label): self
	{
		$this->attributes['no_items_label'] = $label;

		return $this;
	}

	public function setFormAction(string $action): self
	{
		$url = filter_var($action, FILTER_VALIDATE_URL);

		if ($url === false) {
			throw new InvalidArgumentException('You should provide a valid URL');
		}

		$this->attributes['base_href'] = $url;

		$this->addFormData(['href' => $url]);

		return $this;
	}

	public function setDefaultSortColumn(string $column): self
	{
		$this->attributes['default_sort_col'] = $column;

		return $this;
	}

	public function withParams(
		?int $perPage = null,
		?string $noItemsLabel = null,
		?string $action = null,
		?string $defaultSortColumn = null
	): self
	{
		if ($perPage !== null) {
			$this->paginate($perPage);
		}

		if ($noItemsLabel !== null) {
			$this->setNoItemsLabel($noItemsLabel);
		}

		if ($action !== null) {
			$this->setFormAction($action);
		}

		if ($defaultSortColumn !== null) {
			$this->setDefaultSortColumn($defaultSortColumn);
		}

		return $this;
	}

	public function setItems(array|callable $items, array $params = []): self
	{
		$type = match (true) {
			is_callable($items) => 'callable',
			default => 'array',
		};

		$this->attributes['get_items'] = $this->resolveItemsAttribute($type, $items, $params);

		return $this;
	}

	public function setCount(mixed $count, array $params = []): self
	{
		$type = match (true) {
			is_callable($count) => 'callable',
			is_array($count) => 'array',
			default => 'value',
		};

		$this->attributes['get_count'] = $this->resolveCountAttribute($type, $count, $params);

		return $this;
	}

	/**
	 * @param array<Column> $columns
	 */
	public function addColumns(array $columns, ?string $class = null, ?string $style = null): self
	{
		foreach ($columns as $column) {
			if (! $column instanceof Column) {
				throw new InvalidArgumentException(
					sprintf(
						'Column must be instance of %s, %s given',
						Column::class,
						get_debug_type($column)
					)
				);
			}

			$class && $column->setClass($class);
			$style && $column->setStyle($style);
			$this->addColumn($column);
		}

		return $this;
	}

	public function addColumn(Column $column): self
	{
		$this->attributes['columns'][$column->getName()] = $column->toArray();

		return $this;
	}

	public function removeColumn(string $name): self
	{
		unset($this->attributes['columns'][$name]);

		return $this;
	}

	public function addFormData(array $data): self
	{
		$this->attributes['form'] = array_merge($this->attributes['form'] ?? [], $data);

		return $this;
	}

	public function addHiddenFields(array $fields): self
	{
		$this->attributes['form']['hidden_fields'] = array_merge(
			$this->attributes['form']['hidden_fields'] ?? [], $fields
		);

		return $this;
	}

	public function setScript(string $script): self
	{
		$this->attributes['javascript'] = $script;

		return $this;
	}

	public function removeScript(): self
	{
		unset($this->attributes['javascript']);

		return $this;
	}

	/**
	 * @param array<Row> $rows
	 */
	public function addRows(array $rows, ?string $class = null, ?string $style = null): self
	{
		foreach ($rows as $row) {
			if (! $row instanceof Row) {
				throw new InvalidArgumentException(
					sprintf(
						'Row must be instance of %s, %s given',
						Row::class,
						get_debug_type($row)
					)
				);
			}

			$class && $row->setClass($class);
			$style && $row->setStyle($style);
			$this->addRow($row);
		}

		return $this;
	}

	public function addRow(Row $row, ?string $class = null): self
	{
		$class && $row->setClass($class);

		$this->attributes['additional_rows'][] = $row->toArray();

		return $this;
	}

	public function getId(): string
	{
		return $this->attributes['id'];
	}

	public function getTitle(): string
	{
		return $this->attributes['title'];
	}

	public function build(): array
	{
		return $this->attributes;
	}

	private function resolveItemsAttribute(string $type, array|callable $items, array $params): array
	{
		return match ($type) {
			'callable' => [
				'function' => $items,
				'params'   => $params,
			],
			default => [
				'function' => fn($start, $limit, $sort) => $items,
				'params'   => $params,
			],
		};
	}

	private function resolveCountAttribute(string $type, mixed $count, array $params): array
	{
		return match ($type) {
			'callable' => [
				'function' => $count,
				'params'   => $params,
			],
			'array' => [
				'function' => fn() => $count,
				'params'   => $params,
			],
			default => [
				'value'  => (int) $count,
				'params' => $params,
			],
		};
	}
}

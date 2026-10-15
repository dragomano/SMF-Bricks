<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\HasShortMethods;
use Bugo\Bricks\Tables\Interfaces\ColumnInterface;
use Bugo\Bricks\Tables\Interfaces\RowInterface;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use InvalidArgumentException;

use function array_merge;
use function array_slice;
use function count;
use function filter_var;
use function get_debug_type;
use function is_array;
use function is_callable;
use function sprintf;
use function str_contains;
use function str_starts_with;

/**
 * @method self id(string $id)
 * @method self name(string $name)
 * @method self title(string $title)
 * @method self items(array<int|string, mixed>|callable $items, array<string, mixed> $params = [])
 * @method self count(mixed $count, array<string, mixed> $params = [])
 * @method self script(string $script)
 *
 * @phpstan-consistent-constructor
 */
class TableBuilder implements TableBuilderInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
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

	public function setId(string $id): static
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	public function setName(string $name): static
	{
		$this->attributes['form']['name'] = $name;

		return $this;
	}

	public function setTitle(string $title): static
	{
		$this->attributes['title'] = $title;

		return $this;
	}

	public function paginate(int $itemsPerPage = 15): static
	{
		$this->attributes['items_per_page'] = $itemsPerPage;

		return $this;
	}

	public function setNoItemsLabel(string $label): static
	{
		$this->attributes['no_items_label'] = $label;

		return $this;
	}

	public function setFormAction(string $action): static
	{
		if (! $this->isValidAction($action)) {
			throw new InvalidArgumentException('You should provide a valid URL');
		}

		$this->attributes['base_href'] = $action;

		$this->addFormData(['href' => $action]);

		return $this;
	}

	public function setDefaultSortColumn(string $column): static
	{
		$this->attributes['default_sort_col'] = $column;

		return $this;
	}

	public function withParams(
		?int $perPage = null,
		?string $noItemsLabel = null,
		?string $action = null,
		?string $defaultSortColumn = null
	): static
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

	/**
	 * @param array<int|string, mixed>|callable $items
	 * @param array<string, mixed> $params
	 */
	public function setItems(array|callable $items, array $params = []): static
	{
		$this->attributes['get_items'] = is_callable($items)
			? [
				'function' => $items,
				'params'   => $params,
			]
			: [
				'function' => fn($start, $limit, $sort) => array_slice($items, (int) $start, (int) $limit),
				'params'   => $params,
			];

		return $this;
	}

	/**
	 * @param array<string, mixed> $params
	 */
	public function setCount(mixed $count, array $params = []): static
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
	 * @param array<ColumnInterface> $columns
	 */
	public function addColumns(array $columns, ?string $class = null, ?string $style = null): static
	{
		foreach ($columns as $column) {
			if (! $column instanceof ColumnInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Column must be instance of %s, %s given',
						ColumnInterface::class,
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

	public function addColumn(ColumnInterface $column): static
	{
		$this->attributes['columns'][$column->getName()] = $column->toArray();

		return $this;
	}

	public function removeColumn(string $name): static
	{
		unset($this->attributes['columns'][$name]);

		return $this;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function addFormData(array $data): static
	{
		$this->attributes['form'] = array_merge($this->attributes['form'] ?? [], $data);

		return $this;
	}

	/**
	 * @param array<string, mixed> $fields
	 */
	public function addHiddenFields(array $fields): static
	{
		$this->attributes['form']['hidden_fields'] = array_merge(
			$this->attributes['form']['hidden_fields'] ?? [], $fields
		);

		return $this;
	}

	public function setScript(string $script): static
	{
		$this->attributes['javascript'] = $script;

		return $this;
	}

	public function removeScript(): static
	{
		unset($this->attributes['javascript']);

		return $this;
	}

	/**
	 * @param array<RowInterface> $rows
	 */
	public function addRows(array $rows, ?string $class = null, ?string $style = null): static
	{
		foreach ($rows as $row) {
			if (! $row instanceof RowInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Row must be instance of %s, %s given',
						RowInterface::class,
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

	public function addRow(RowInterface $row, ?string $class = null): static
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

	/**
	 * @return array<string, mixed>
	 */
	public function build(): array
	{
		return $this->attributes;
	}

	/**
	 * Accepts absolute URLs as well as relative ones (e.g. "index.php?action=admin"
	 * or "/forum/index.php"), because SMF may provide a relative form action.
	 */
	private function isValidAction(string $action): bool
	{
		if (filter_var($action, FILTER_VALIDATE_URL) !== false) {
			return true;
		}

		return str_contains($action, '/')
			|| str_contains($action, '.php')
			|| str_starts_with($action, '?');
	}

	/**
	 * @param array<string, mixed> $params
	 *
	 * @return array<string, mixed>
	 */
	private function resolveCountAttribute(string $type, mixed $count, array $params): array
	{
		return match ($type) {
			'callable' => [
				'function' => $count,
				'params'   => $params,
			],
			'array' => [
				'function' => fn() => count($count),
				'params'   => $params,
			],
			default => [
				'value'  => (int) $count,
				'params' => $params,
			],
		};
	}
}

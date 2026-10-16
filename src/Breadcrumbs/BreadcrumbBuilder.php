<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbItemInterface;
use InvalidArgumentException;
use Stringable;

use function get_debug_type;
use function in_array;
use function is_scalar;
use function sprintf;

/**
 * @phpstan-consistent-constructor
 */
class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
	/** @var array<int, BreadcrumbItemInterface> */
	private array $items = [];

	protected function __construct() {}

	public static function make(): static
	{
		return new static();
	}

	public function add(string $name, ?string $url = null, ?string $before = null, ?string $after = null): self
	{
		$this->items[] = BreadcrumbItem::make($name, $url, $before, $after);

		return $this;
	}

	public function addItem(BreadcrumbItemInterface $item): self
	{
		$this->items[] = $item;

		return $this;
	}

	/**
	 * @param array<BreadcrumbItemInterface> $items
	 */
	public function addItems(array $items): self
	{
		foreach ($items as $item) {
			if (! $item instanceof BreadcrumbItemInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Item must be instance of %s, %s given',
						BreadcrumbItemInterface::class,
						get_debug_type($item)
					)
				);
			}

			$this->addItem($item);
		}

		return $this;
	}

	public function update(int $index, string $key, mixed $value): self
	{
		if (! isset($this->items[$index]))
			return $this;

		if (! in_array($key, ['name', 'url', 'before', 'after'], true)) {
			return $this;
		}

		if ($value !== null && ! is_scalar($value) && ! $value instanceof Stringable) {
			throw new InvalidArgumentException('Breadcrumb values must be scalars, stringable objects or null');
		}

		$item = $this->items[$index];

		$name   = $item->getName();
		$url    = $item->getUrl();
		$before = $item->getBefore();
		$after  = $item->getAfter();

		switch ($key) {
			case 'name':
				$name = (string) $value;
				break;

			case 'url':
				$url = $value !== null ? (string) $value : null;
				break;

			case 'before':
				$before = $value !== null ? (string) $value : null;
				break;

			case 'after':
				$after = $value !== null ? (string) $value : null;
				break;
		}

		// The item is rebuilt through its own factory, so a subclass keeps its class.
		$this->items[$index] = $item::make($name, $url, $before, $after);

		return $this;
	}

	public function remove(int $index): self
	{
		if (isset($this->items[$index])) {
			unset($this->items[$index]);

			$this->items = array_values($this->items);
		}

		return $this;
	}

	/**
	 * @return array<int, BreadcrumbItemInterface>
	 */
	public function getAll(): array
	{
		return $this->items;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function getByIndex(int $index): ?array
	{
		return isset($this->items[$index]) ? $this->items[$index]->toArray() : null;
	}

	/**
	 * Only valid items are returned: an element without a name is skipped instead of being
	 * replaced with an empty string.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function build(): array
	{
		$items = [];

		foreach ($this->getAll() as $item) {
			$data = $item->toArray();

			if (($data['name'] ?? '') === '') {
				continue;
			}

			$items[] = $data;
		}

		return $items;
	}
}

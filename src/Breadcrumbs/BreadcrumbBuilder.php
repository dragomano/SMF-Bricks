<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use InvalidArgumentException;

use function array_map;
use function get_debug_type;
use function sprintf;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
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

	public function addItem(BreadcrumbItem $item): self
	{
		$this->items[] = $item;

		return $this;
	}

	/**
	 * @param array<BreadcrumbItem> $items
	 */
	public function addItems(array $items): self
	{
		foreach ($items as $item) {
			if (! $item instanceof BreadcrumbItem) {
				throw new InvalidArgumentException(
					sprintf(
						'Item must be instance of %s, %s given',
						BreadcrumbItem::class,
						get_debug_type($item)
					)
				);
			}

			$this->addItem($item);
		}

		return $this;
	}

	public function remove(int $index): self
	{
		if (isset($this->items[$index])) {
			unset($this->items[$index]);
		}

		return $this;
	}

	public function clear(): void
	{
		$this->items = [];
	}

	public function build(): array
	{
		return array_map(fn(BreadcrumbItem $item) => $item->toArray() ?: '', $this->items);
	}
}

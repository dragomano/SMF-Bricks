<?php declare(strict_types=1);

namespace Bugo\Bricks\Breadcrumbs;

use Bugo\Bricks\Breadcrumbs\Interfaces\BreadcrumbBuilderInterface;
use InvalidArgumentException;

use function array_map;
use function get_debug_type;
use function sprintf;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    /** @var array<int, BreadcrumbItem> */
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

	public function update(int $index, string $key, mixed $value): self
	{
		if (! isset($this->items[$index]))
			return $this;

		$item = $this->items[$index];

		switch ($key) {
			case 'name':
				$this->items[$index] = BreadcrumbItem::make(
					(string) $value,
					$item->getUrl(),
					$item->getBefore(),
					$item->getAfter()
				);
				break;

			case 'url':
				$this->items[$index] = BreadcrumbItem::make(
					$item->getName(),
					$value !== null ? (string) $value : null,
					$item->getBefore(),
					$item->getAfter()
				);
				break;

			case 'before':
				$item->setBefore($value !== null ? (string) $value : null);
				break;

			case 'after':
				$item->setAfter($value !== null ? (string) $value : null);
				break;

			default:
				break;
		}

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

	public function getAll(): array
	{
		return $this->items;
	}

	public function getByIndex(int $index): ?array
	{
		return isset($this->items[$index]) ? $this->items[$index]->toArray() : null;
	}

	public function build(): array
	{
		return array_map(fn(BreadcrumbItem $item) => $item->toArray() ?: '', $this->getAll());
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\HasShortMethods;
use Bugo\Bricks\Tables\Interfaces\ColumnInterface;
use Closure;

use function is_string;

/**
 * @method self class(string $class)
 * @method self style(mixed $value)
 * @method self data(mixed $value)
 * @method self sort(mixed $value)
 * @method self headerClass(string $class)
 * @method self headerStyle(string $style)
 *
 * @phpstan-consistent-constructor
 */
class Column implements ColumnInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
	protected array $attributes = [];

	protected function __construct(private readonly string $name, string $title)
	{
		$this->attributes['header']['value'] = $title;
	}

	public static function make(string $name, string $title): static
	{
		return new static($name, $title);
	}

	/**
	 * Sets the CSS class of the column header. Use setData() for data cell classes.
	 */
	public function setClass(string $class): static
	{
		$this->attributes['header']['class'] = $class;

		return $this;
	}

	/**
	 * Sets the CSS class of the column header (an alias of setClass()).
	 */
	public function setHeaderClass(string $class): static
	{
		return $this->setClass($class);
	}

	/**
	 * Sets the inline style of the column header. Use setData() for data cell styles.
	 */
	public function setStyle(string $style): static
	{
		$this->attributes['header']['style'] = $style;

		return $this;
	}

	/**
	 * Sets the inline style of the column header (an alias of setStyle()).
	 */
	public function setHeaderStyle(string $style): static
	{
		return $this->setStyle($style);
	}

	/**
	 * @param array<string, mixed>|Closure|string $data
	 */
	public function setData(Closure|string|array $data, ?string $class = null, ?string $style = null): static
	{
		if (is_string($data)) {
			$this->attributes['data'] = ['db' => $data];
		} elseif ($data instanceof Closure) {
			$this->attributes['data'] = ['function' => $data];
		} else {
			$this->attributes['data'] = $data;
		}

		if ($class) {
			$this->attributes['data']['class'] = $class;
		}

		if ($style) {
			$this->attributes['data']['style'] = $style;
		}

		return $this;
	}

	public function setSort(string $default, ?string $reverse = null): static
	{
		$this->attributes['sort'] = [
			'default' => $default,
			'reverse' => $reverse ?? "$default DESC"
		];

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return $this->attributes;
	}
}

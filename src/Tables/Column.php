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
 */
class Column implements ColumnInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
	private array $attributes = [];

	private function __construct(private readonly string $name, string $title)
	{
		$this->attributes['header']['value'] = $title;
	}

	public static function make(string $name, string $title): static
	{
		return new static($name, $title);
	}

	public function setClass(string $class): static
	{
		$this->attributes['header']['class'] = $class;

		return $this;
	}

	public function setStyle(string $style): static
	{
		$this->attributes['header']['style'] = $style;

		return $this;
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

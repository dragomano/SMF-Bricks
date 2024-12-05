<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables\Interfaces;

use Bugo\Bricks\BrickInterface;
use Closure;

interface ColumnInterface extends BrickInterface
{
	public static function make(string $name, string $title): static;

	public function setData(Closure|string|array $data, string $class = '', string $style = ''): static;

	public function setSort(string $default, ?string $reverse = null): static;

	public function getName(): string;
}

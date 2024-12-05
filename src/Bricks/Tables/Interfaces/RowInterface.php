<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables\Interfaces;

use Bugo\Bricks\BrickInterface;
use Bugo\Bricks\Tables\RowPosition;

interface RowInterface extends BrickInterface
{
	public static function make(string $value, string $class = ''): static;

	public function setValue(string $value): static;

	public function setPosition(RowPosition|string $position): static;
}

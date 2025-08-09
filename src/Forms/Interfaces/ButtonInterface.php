<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

use Bugo\Bricks\BrickInterface;

interface ButtonInterface extends BrickInterface
{
	public static function make(string $name, string $value): static;

	public function setId(string $id): static;

	public function setType(string $type): static;

	public function setValue(mixed $value): static;

	public function setAttributes(array $attributes): static;

	public function getName(): string;

	public function getValue(): string;

	public function getType(): string;
}

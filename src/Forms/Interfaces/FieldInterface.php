<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

use Bugo\Bricks\BrickInterface;

interface FieldInterface extends BrickInterface
{
	public static function make(string $name, string $label): static;

	public function setId(string $id): static;

	public function setType(string $type): static;

	public function setValue(mixed $value): static;

	public function required(bool $required = true): static;

	public function readonly(bool $readonly = true): static;

	public function disabled(bool $disabled = true): static;

	public function setAttributes(array $attributes): static;

	public function getName(): string;

	public function getValue(): mixed;

	public function getType(): string;
}

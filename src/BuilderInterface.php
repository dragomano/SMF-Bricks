<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface BuilderInterface
{
	public static function make(string $id, string $title): static;

	public function setId(string $id): static;

	public function setName(string $name): static;

	public function setTitle(string $title): static;

	public function getId(): string;

	public function getTitle(): string;

	public function build(): array;
}

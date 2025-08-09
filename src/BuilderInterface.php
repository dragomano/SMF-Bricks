<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface BuilderInterface
{
	public static function make(string $id, string $title): static;

	public function setId(string $id): self;

	public function setName(string $name): self;

	public function setTitle(string $title): self;

	public function getId(): string;

	public function getTitle(): string;

	public function build(): array;
}

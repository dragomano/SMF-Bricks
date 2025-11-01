<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings\Interfaces;

interface ConfigInterface
{
	public static function make(string $name): static;

	public function setAttributes(array $attributes): self;

	public function setAttribute(string $name, string $value): self;

	public function setLabel(string $label): self;

	public function setHelp(string $help): self;

	public function setPreInput(string $input): self;

	public function setPostInput(string $input): self;

	public function setSubText(string $subtext): self;

	public function setJavaScript(string $javascript): self;

	public function setOnChange(string $onchange): self;

	public function setDisabled(bool $disabled): self;

	public function setInvalid(bool $invalid): self;

	public function setTab(string $tab): self;

	public function toArray(): array;
}

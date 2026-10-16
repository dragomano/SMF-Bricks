<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings\Interfaces;

interface ConfigBuilderInterface
{
	public static function make(): static;

	/**
	 * @param array<ConfigInterface> $vars
	 */
	public function addVars(array $vars): self;

	public function addVar(ConfigInterface $var): self;

	/**
	 * @return array<int, array<int|string, mixed>|string>
	 */
	public function build(): array;
}

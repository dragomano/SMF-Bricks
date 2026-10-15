<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings\Interfaces;

use Bugo\Bricks\Settings\AbstractConfig;

interface ConfigBuilderInterface
{
	public static function make(): static;

	/**
	 * @param array<ConfigInterface> $vars
	 */
	public function addVars(array $vars): self;

	public function addVar(AbstractConfig $var): self;

	/**
	 * @return array<int, array<int|string, mixed>|string>
	 */
	public function build(): array;
}

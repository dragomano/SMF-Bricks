<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings\Interfaces;

use Bugo\Bricks\Settings\AbstractConfig;

interface ConfigBuilderInterface
{
	public static function make(): static;

	public function addVars(array $vars): self;

	public function addVar(AbstractConfig $var): self;

	public function build(): array;
}

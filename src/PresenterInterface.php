<?php declare(strict_types=1);

namespace Bugo\Bricks;

interface PresenterInterface
{
	public static function show(BuilderInterface $builder): void;
}

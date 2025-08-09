<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables\Interfaces;

interface TablePresenterInterface
{
	public function show(TableBuilderInterface $builder): void;
}

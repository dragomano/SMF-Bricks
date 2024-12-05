<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;

interface TablePresenterInterface
{
	public static function show(TableBuilderInterface $builder): void;
}

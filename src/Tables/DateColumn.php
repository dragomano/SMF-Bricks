<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

class DateColumn extends Column
{
	public static function make(string $name = 'date', string $title = 'Date'): static
	{
		return parent::make($name, $title)
			->setData('date', '', 'text-align: center')
			->setSort('date DESC', 'date');
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

class IdColumn extends Column
{
	public static function make(string $name = 'id', string $title = '#'): static
	{
		return parent::make($name, $title)
			->setStyle('width: 5%')
			->setData($name, '', 'text-align: center')
			->setSort("$name DESC", $name);
	}
}

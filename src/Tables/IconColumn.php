<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

class IconColumn extends Column
{
	public static function make(string $name = 'icon', string $title = 'Icon'): static
	{
		return parent::make($name, $title)
			->setStyle('width: 10%')
			->setData('icon', '', 'text-align: center')
			->setSort('icon');
	}
}

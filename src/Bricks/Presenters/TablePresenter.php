<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\ItemList;
use Bugo\Compat\Utils;

use function array_filter;
use function in_array;

use const ARRAY_FILTER_USE_KEY;

class TablePresenter extends AbstractTablePresenter
{
	public static function show(TableBuilderInterface $builder): void
	{
		$data = $builder->build();

		$data['columns'] = array_filter(
			$data['columns'] ?? [],
			fn($column) => ! in_array($column, static::$excludeColumns),
			ARRAY_FILTER_USE_KEY
		);

		new ItemList($data);

		Utils::$context['sub_template'] = 'show_list';
		Utils::$context['default_list'] = $builder->getId();
	}
}

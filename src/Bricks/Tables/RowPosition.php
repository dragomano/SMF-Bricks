<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use function strtolower;

enum RowPosition
{
	case ABOVE_COLUMN_HEADERS;
	case AFTER_TITLE;
	case BELOW_TABLE_DATA;
	case BOTTOM_OF_LIST;
	case TOP_OF_LIST;

	public function name(): string
	{
		return strtolower($this->name);
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

abstract class AbstractTablePresenter implements TablePresenterInterface
{
	protected static array $excludeColumns = [];

	public function excludeColumns(array $columns): static
	{
		static::$excludeColumns = $columns;

		return $this;
	}
}

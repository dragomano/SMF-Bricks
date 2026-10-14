<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\Tables\Interfaces\TablePresenterInterface;

abstract class AbstractTablePresenter implements TablePresenterInterface
{
	protected array $excludeColumns = [];

	public function excludeColumns(array $columns): static
	{
		$this->excludeColumns = $columns;

		return $this;
	}
}

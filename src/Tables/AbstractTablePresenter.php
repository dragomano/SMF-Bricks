<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\Tables\Interfaces\TablePresenterInterface;

abstract class AbstractTablePresenter implements TablePresenterInterface
{
	/** @var array<int, string> */
	protected array $excludeColumns = [];

	/**
	 * @param array<int, string> $columns
	 */
	public function excludeColumns(array $columns): static
	{
		$this->excludeColumns = $columns;

		return $this;
	}
}

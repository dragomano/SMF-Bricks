<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\DisplaysOutput;
use Bugo\Bricks\RendererInterface;
use Bugo\Bricks\StringRendererInterface;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use InvalidArgumentException;

use function array_filter;
use function in_array;
use function is_array;

use const ARRAY_FILTER_USE_KEY;

class TablePresenter extends AbstractTablePresenter
{
	use DisplaysOutput;

	public function __construct(private readonly RendererInterface|StringRendererInterface $renderer) {}

	public function show(TableBuilderInterface $builder): void
	{
		$data = $builder->build();
		$columns = $data['columns'] ?? [];

		if (! is_array($columns)) {
			throw new InvalidArgumentException('Table columns must be an array');
		}

		$data['columns'] = array_filter(
			$columns,
			fn($column) => ! in_array($column, $this->excludeColumns, true),
			ARRAY_FILTER_USE_KEY
		);

		$this->display($data);
	}
}

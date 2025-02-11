<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Renderers\RendererInterface;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;

use function array_filter;
use function in_array;

use const ARRAY_FILTER_USE_KEY;

class TablePresenter extends AbstractTablePresenter
{
	public function __construct(private readonly RendererInterface $renderer) {}

	public function show(TableBuilderInterface $builder): void
	{
		$data = $builder->build();

		$data['columns'] = array_filter(
			$data['columns'] ?? [],
			fn($column) => ! in_array($column, static::$excludeColumns),
			ARRAY_FILTER_USE_KEY
		);

		$this->renderer->render($data);
	}
}

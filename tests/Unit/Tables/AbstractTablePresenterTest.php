<?php declare(strict_types=1);

use Bugo\Bricks\RendererInterface;
use Bugo\Bricks\Tables\AbstractTablePresenter;
use Bugo\Bricks\Tables\Column;
use Bugo\Bricks\Tables\IdColumn;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Bricks\Tables\TableBuilder;
use Bugo\Bricks\Tables\TablePresenter;
use Bugo\Bricks\Tables\TableRenderer;

it('renders output correctly', function () {
	$testPresenter = new class extends AbstractTablePresenter {
		public function show(TableBuilderInterface $builder): void
		{
			echo 'Test output';
		}
	};

	$builder = mock(TableBuilderInterface::class);

	ob_start();
	$testPresenter->excludeColumns(['test']);
	$testPresenter->show($builder);
	$output = ob_get_clean();

	expect(trim($output))->toBe('Test output');
});

it('keeps excluded columns per presenter instance', function () {
	$captured = [];

	$renderer = mock(RendererInterface::class);
	$renderer->shouldReceive('render')->andReturnUsing(function (array $data) use (&$captured) {
		$captured = $data['columns'];
	});

	$builder = TableBuilder::make('table_id', 'Title')
		->addColumn(IdColumn::make())
		->addColumn(Column::make('title', 'Title'));

	$first  = new TablePresenter($renderer);
	$second = new TablePresenter($renderer);

	$first->excludeColumns(['id']);

	$second->show($builder);

	expect($captured)->toHaveKeys(['id', 'title']);

	$first->show($builder);

	expect($captured)->toHaveKeys(['title'])
		->and($captured)->not->toHaveKey('id');
});

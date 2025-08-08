<?php declare(strict_types=1);

use Bugo\Bricks\Tables\AbstractTablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;

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

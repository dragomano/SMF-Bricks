<?php declare(strict_types=1);

use Bugo\Bricks\Presenters\CsvTablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

it('renders CSV correctly', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(Utils::$context['table_id']);
	$builder->shouldReceive('removeScript')->andReturn($builder);
	$builder->shouldReceive('getId')->andReturn('table_id');

	ob_start();
	CsvTablePresenter::show($builder);
	$output = ob_get_clean();

	$expectedOutput = "Username,Email\n";
	$expectedOutput .= "test_user,test@example.com\n";
	$expectedOutput .= "another_user,another@example.com\n";

	expect($output)->toContain($expectedOutput);
});

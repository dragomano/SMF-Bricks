<?php declare(strict_types=1);

use Bugo\Bricks\Presenters\TablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

it('renders a table correctly', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(Utils::$context['table_id']);
	$builder->shouldReceive('removeScript')->andReturn($builder);
	$builder->shouldReceive('getId')->andReturn('table_id');

	ob_start();
	TablePresenter::show($builder);
	$output = ob_get_clean();

	expect($output)->toContain('<table id="table_id"')
		->and($output)->toContain('Test Table')
		->and($output)->toContain('Username')
		->and($output)->toContain('Email')
		->and($output)->toContain('test_user')
		->and($output)->toContain('test@example.com');
});

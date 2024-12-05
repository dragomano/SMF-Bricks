<?php declare(strict_types=1);

use Bugo\Bricks\Presenters\XmlTablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

it('renders XML correctly', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(Utils::$context['table_id']);
	$builder->shouldReceive('removeScript')->andReturn($builder);
	$builder->shouldReceive('getId')->andReturn('table_id');

	ob_start();
	XmlTablePresenter::show($builder);
	$output = ob_get_clean();

	expect($output)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
		->and($output)->toContain('table_id')
		->and($output)->toContain('Username')
		->and($output)->toContain('Email')
		->and($output)->toContain('test_user')
		->and($output)->toContain('test@example.com')
		->and($output)->toContain('another_user')
		->and($output)->toContain('another@example.com');
});

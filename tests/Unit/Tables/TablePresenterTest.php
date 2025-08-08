<?php declare(strict_types=1);

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Bricks\Tables\TablePresenter;
use Bugo\Bricks\Tables\TableRenderer;

it('renders a table correctly', function () {
	$data = [
		'id' => 'table_id',
		'title' => 'Test Table',
		'items_per_page' => 10,
		'headers' => [
			['id' => 'username', 'label' => 'Username'],
			['id' => 'email', 'label' => 'Email'],
		],
		'rows' => [
			['data' => ['username' => ['value' => 'test_user'], 'email' => ['value' => 'test@example.com']]],
			['data' => ['username' => ['value' => 'another_user'], 'email' => ['value' => 'another@example.com']]],
		],
		'javascript' => 'console.log("test");'
	];

	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn($data);

	ob_start();
	$renderer = new TableRenderer();
	$presenter = new TablePresenter($renderer);
	$presenter->show($builder);
	$output = ob_get_clean();

	expect($output)->toContain('<table id="table_id"')
		->and($output)->toContain('Test Table')
		->and($output)->toContain('Username')
		->and($output)->toContain('Email')
		->and($output)->toContain('test_user')
		->and($output)->toContain('test@example.com')
		->and($output)->toContain('console.log("test")');
});

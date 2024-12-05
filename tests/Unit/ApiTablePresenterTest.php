<?php declare(strict_types=1);

use Bugo\Bricks\Presenters\ApiTablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

it('renders JSON API correctly', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(Utils::$context['table_id']);
	$builder->shouldReceive('removeScript')->andReturn($builder);
	$builder->shouldReceive('getId')->andReturn('table_id');

	ob_start();
	ApiTablePresenter::show($builder);
	$output = ob_get_clean();

	$expectedOutput = json_encode([
		'data' => [
			['username' => ['value' => 'test_user'], 'email' => ['value' => 'test@example.com']],
			['username' => ['value' => 'another_user'], 'email' => ['value' => 'another@example.com']],
		],
		'meta' => [
			'total' => 2,
			'per_page' => 10,
			'current_page' => 1,
			'columns' => [
				['id' => 'username', 'label' => 'Username'],
				['id' => 'email', 'label' => 'Email'],
			],
		],
	], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	expect($output)->toBe($expectedOutput);
});

<?php declare(strict_types=1);

use Bugo\Bricks\Presenters\JsonTablePresenter;
use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

it('renders JSON correctly', function () {
	$builder = mock(TableBuilderInterface::class);
	$builder->shouldReceive('build')->andReturn(Utils::$context['table_id']);
	$builder->shouldReceive('removeScript')->andReturn($builder);
	$builder->shouldReceive('getId')->andReturn('table_id');

	ob_start();
	JsonTablePresenter::show($builder);
	$output = ob_get_clean();

	expect($output)->toBe(json_encode(Utils::$context['table_id'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
		->and($output)->toBeJson();
});

<?php declare(strict_types=1);

use Bugo\Bricks\Settings\CheckConfig;
use Bugo\Bricks\Settings\ConfigBuilder;
use Bugo\Bricks\Settings\DividerConfig;

beforeEach(function () {
	$this->builder = ConfigBuilder::make();
});

it('can validate vars on adding', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addVars([
		['array instead of ConfigInterface class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can set a group of vars', function () {
	$this->builder->addVars([
		CheckConfig::make('some_check'),
		DividerConfig::make(),
	]);

	expect($this->builder->build())->toBe([
		[
			'check',
			'some_check',
		],
		'',
	]);
});

it('can set a single var', function () {
	$this->builder->addVar(CheckConfig::make('some_check'));

	expect($this->builder->build())->toBe([
		[
			'check',
			'some_check',
		]
	]);
});

<?php declare(strict_types=1);

use Bugo\Bricks\Settings\IntConfig;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

beforeEach(function () {
	$this->config = IntConfig::make('foo_bar');
});

it('is an instance of ConfigInterface class', function () {
	expect($this->config)->toBeInstanceOf(ConfigInterface::class);
});

it('can set a int', function () {
	expect($this->config->toArray())->toBe([
		'int',
		'foo_bar',
		'step' => 1,
	]);
});

it('can set a min value', function () {
	expect($this->config->setMin(1)->toArray())->toBe([
		'int',
		'foo_bar',
		'min' => 1,
		'step' => 1,
	]);
});

it('can set a max value', function () {
	expect($this->config->setMax(2)->toArray())->toBe([
		'int',
		'foo_bar',
		'max' => 2,
		'step' => 1,
	]);
});

it('can set a step value', function () {
	expect($this->config->setStep(1)->toArray())->toBe([
		'int',
		'foo_bar',
		'step' => 1,
	]);
});

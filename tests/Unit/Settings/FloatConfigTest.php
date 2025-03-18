<?php declare(strict_types=1);

use Bugo\Bricks\Settings\FloatConfig;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

beforeEach(function () {
	$this->config = FloatConfig::make('foo_bar');
});

it('is an instance of ConfigInterface class', function () {
	expect($this->config)->toBeInstanceOf(ConfigInterface::class);
});

it('can set a float', function () {
	expect($this->config->toArray())->toBe([
		'float',
		'foo_bar',
		'step' => 0.1,
	]);
});

it('can set a min value', function () {
	expect($this->config->setMin(1)->toArray())->toBe([
		'float',
		'foo_bar',
		'min' => 1.0,
		'step' => 0.1,
	]);
});

it('can set a max value', function () {
	expect($this->config->setMax(2)->toArray())->toBe([
		'float',
		'foo_bar',
		'max' => 2.0,
		'step' => 0.1,
	]);
});

it('can set a step value', function () {
	expect($this->config->setStep(1)->toArray())->toBe([
		'float',
		'foo_bar',
		'step' => 1.0,
	]);
});

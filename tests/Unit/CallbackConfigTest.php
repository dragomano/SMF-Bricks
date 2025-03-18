<?php declare(strict_types=1);

use Bugo\Bricks\Settings\CallbackConfig;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

beforeEach(function () {
	$this->config = CallbackConfig::make('foo_bar');
});

it('is an instance of ConfigInterface class', function () {
	expect($this->config)->toBeInstanceOf(ConfigInterface::class);
});

it('can set a callback', function () {
	$callback = fn() => 'value';

	expect($this->config->setCallback($callback)->toArray())->toBe([
		'callback',
		'foo_bar',
		'callback' => $callback,
	]);
});

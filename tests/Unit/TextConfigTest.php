<?php declare(strict_types=1);

use Bugo\Bricks\Settings\TextConfig;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

beforeEach(function () {
	$this->config = TextConfig::make('foo_bar');
});

it('is an instance of ConfigInterface class', function () {
	expect($this->config)->toBeInstanceOf(ConfigInterface::class);
});

it('can set a text config', function () {
	expect($this->config->toArray())->toBe([
		'text',
		'foo_bar',
	]);
});

it('can set a placeholder', function () {
	$placeholder = 'some_placeholder';

	expect($this->config->setPlaceholder($placeholder)->toArray())->toBe([
		'text',
		'foo_bar',
		'placeholder' => $placeholder,
	]);
});

it('can set a size', function () {
	$size = '100';

	expect($this->config->setSize($size)->toArray())->toBe([
		'text',
		'foo_bar',
		'size' => $size,
	]);
});

<?php declare(strict_types=1);

use Bugo\Bricks\Settings\SelectConfig;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

beforeEach(function () {
	$this->config = SelectConfig::make('foo_bar');
});

it('is an instance of ConfigInterface class', function () {
	expect($this->config)->toBeInstanceOf(ConfigInterface::class);
});

it('can set options', function () {
	$options = [1, 2, 3];

	expect($this->config->setOptions($options)->toArray())->toBe([
		'select',
		'foo_bar',
		$options,
		'options' => $options,
	]);
});

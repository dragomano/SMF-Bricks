<?php declare(strict_types=1);

use Bugo\Bricks\Settings\CallbackConfig;
use Bugo\Bricks\Settings\CheckConfig;
use Bugo\Bricks\Settings\DividerConfig;
use Bugo\Bricks\Settings\FloatConfig;
use Bugo\Bricks\Settings\IntConfig;
use Bugo\Bricks\Settings\PermissionsConfig;
use Bugo\Bricks\Settings\SelectConfig;
use Bugo\Bricks\Settings\TextConfig;
use Bugo\Bricks\Settings\TitleConfig;

/**
 * Runs the callback with every PHP warning converted into an exception.
 */
function configToArrayQuietly(callable $callback): array
{
	set_error_handler(static function (int $severity, string $message): bool {
		throw new ErrorException($message);
	});

	try {
		return $callback();
	} finally {
		restore_error_handler();
	}
}

/**
 * The output of *Config::toArray() is consumed both by SMF (prepareDBSettingContext()
 * copies every non-numeric key into $context['config_vars']) and by Light Portal,
 * which reads $var['name'], $var['type'], $var['tab'], $var['attributes'],
 * $var['callback'] and the positional $configVars[$i][2]. These tests lock that contract.
 */
it('keeps the positional contract (type, name) for every config type', function () {
	$vars = [
		CheckConfig::make('lp_a')->toArray(),
		IntConfig::make('lp_b')->toArray(),
		FloatConfig::make('lp_c')->toArray(),
		TextConfig::make('lp_d')->toArray(),
		TitleConfig::make('lp_e')->toArray(),
		SelectConfig::make('lp_f')->setOptions(['a' => 'A'])->toArray(),
		CallbackConfig::make('lp_g')->toArray(),
		PermissionsConfig::make('lp_h')->toArray(),
	];

	expect(array_column($vars, 1))->toBe([
		'lp_a', 'lp_b', 'lp_c', 'lp_d', 'lp_e', 'lp_f', 'lp_g', 'lp_h',
	]);
});

it('keeps the tab key produced by setTab()', function () {
	$data = CheckConfig::make('lp_debug')->setTab('base')->toArray();

	expect($data)->toBe([0 => 'check', 1 => 'lp_debug', 'tab' => 'base']);
});

it('keeps select options at index 2 and under the options key', function () {
	$data = SelectConfig::make('lp_frontpage_mode')->setOptions(['a' => 'A', 'b' => 'B'])->toArray();

	expect($data)->toBe([
		0         => 'select',
		1         => 'lp_frontpage_mode',
		2         => ['a' => 'A', 'b' => 'B'],
		'options' => ['a' => 'A', 'b' => 'B'],
	]);
});

it('returns an empty array for DividerConfig (current contract)', function () {
	// Light Portal uses DividerConfig in ExtraConfig: ConfigBuilder::build() turns
	// this into '' and SMF treats a non-array entry as a raw value. Do not "fix" it
	// locally.
	expect(DividerConfig::make()->toArray())->toBe([]);
});

it('keeps the whole output for check config with all setters applied', function () {
	$data = CheckConfig::make('lp_b')
		->setHelp('h')
		->setLabel('L')
		->setPreInput('pre')
		->setPostInput('pi')
		->setSubText('sub')
		->setJavaScript('js')
		->setOnChange('oc')
		->setDisabled(true)
		->setInvalid(true)
		->setAttribute('data-x', '1')
		->setTab('base')
		->toArray();

	expect(array_keys($data))->toBe([
		0, 1,
		'attributes', 'disabled', 'help', 'invalid', 'javascript', 'label',
		'onchange', 'postinput', 'preinput', 'subtext', 'tab',
	])->and($data['attributes'])->toBe(['data-x' => '1'])
		->and($data['disabled'])->toBeTrue()
		->and($data['invalid'])->toBeTrue();
});

it('keeps the whole output for numeric and text configs', function () {
	expect(IntConfig::make('lp_c')->setMin(1)->setMax(10)->setStep(2)->toArray())->toBe([
		0 => 'int', 1 => 'lp_c', 'max' => 10, 'min' => 1, 'step' => 2,
	])->and(IntConfig::make('lp_d')->toArray())->toBe([
		0 => 'int', 1 => 'lp_d', 'step' => 1,
	])->and(FloatConfig::make('lp_e')->toArray())->toBe([
		0 => 'float', 1 => 'lp_e', 'step' => 0.1,
	])->and(TextConfig::make('lp_f')->setPlaceholder('ph')->setSize('75')->toArray())->toBe([
		0 => 'text', 1 => 'lp_f', 'placeholder' => 'ph', 'size' => '75',
	])->and(TitleConfig::make('lp_g')->toArray())->toBe([
		0 => 'title', 1 => 'lp_g',
	])->and(PermissionsConfig::make('lp_j')->toArray())->toBe([
		0 => 'permissions', 1 => 'lp_j',
	]);
});

it('keeps the callback closure under the callback key', function () {
	$callback = static fn() => 'x';

	$data = CallbackConfig::make('lp_i')->setCallback($callback)->toArray();

	expect(array_keys($data))->toBe([0, 1, 'callback'])
		->and($data['callback'])->toBe($callback);
});

it('keeps falsy values except null and an empty string', function () {
	expect(IntConfig::make('lp_min')->setMin(0)->setStep(0)->toArray())->toBe([
		0 => 'int', 1 => 'lp_min', 'min' => 0, 'step' => 0,
	])->and(TextConfig::make('lp_size')->setSize('0')->toArray())->toBe([
		0 => 'text', 1 => 'lp_size', 'size' => '0',
	])->and(CheckConfig::make('lp_disabled')->setDisabled(false)->toArray())->toBe([
		0 => 'check', 1 => 'lp_disabled', 'disabled' => false,
	])->and(FloatConfig::make('lp_max')->setMax(0)->toArray())->toBe([
		0 => 'float', 1 => 'lp_max', 'max' => 0.0, 'step' => 0.1,
	]);
});

it('does not warn when select options are not set', function () {
	$data = configToArrayQuietly(static fn() => SelectConfig::make('lp_select')->toArray());

	expect($data)->toBe([0 => 'select', 1 => 'lp_select', 2 => []]);
});

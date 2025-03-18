<?php declare(strict_types=1);

use Tests\Unit\Settings\SomeConfig;

beforeEach(function () {
	$this->config = SomeConfig::make('foo_bar');
});

it('can set attributes', function () {
	$attributes = [
		'class' => 'some_class',
		'style' => 'some_style',
	];

	expect($this->config->setAttributes($attributes)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'attributes' => [
			'class' => 'some_class',
			'style' => 'some_style',
		],
	]);
});

it('can set a label', function () {
	$label = 'some_label';

	expect($this->config->setLabel($label)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'label' => $label,
	]);
});

it('can set a help', function () {
	$help = 'some_help';

	expect($this->config->setHelp($help)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'help' => $help,
	]);
});

it('can set a preinput', function () {
	$preinput = 'some_preinput';

	expect($this->config->setPreInput($preinput)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'preinput' => $preinput,
	]);
});

it('can set a postinput', function () {
	$postinput = 'some_postinput';

	expect($this->config->setPostInput($postinput)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'postinput' => $postinput,
	]);
});

it('can set a subtext', function () {
	$subtext = 'some_subtext';

	expect($this->config->setSubText($subtext)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'subtext' => $subtext,
	]);
});

it('can set a javascript', function () {
	$javascript = 'console.log("test")';

	expect($this->config->setJavaScript($javascript)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'javascript' => $javascript,
	]);
});

it('can set onchange value', function () {
	$onchange = 'console.log("changed")';

	expect($this->config->setOnChange($onchange)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'onchange' => $onchange,
	]);
});

it('can be disabled', function () {
	expect($this->config->setDisabled(true)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'disabled' => true,
	]);
});

it('can be invalid', function () {
	expect($this->config->setInvalid(true)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'invalid' => true,
	]);
});

it('can set a tab', function () {
	$tab = 'some_tab';

	expect($this->config->setTab($tab)->toArray())->toBe([
		'some_type',
		'foo_bar',
		'tab' => $tab,
	]);
});

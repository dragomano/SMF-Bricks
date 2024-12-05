<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Button;

beforeEach(function () {
	$this->button = Button::make('submit', 'Submit');

	$this->resultArray = [
		'name'  => 'submit',
		'value' => 'Submit',
		'type'  => 'button',
	];
});

it('can set an id', function () {
	expect($this->button->setId('some_id')->toArray()['id'])->toBe('some_id');
});

it('can set a type', function () {
	expect($this->button->setType('submit')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['type' => 'submit']));
});

it('can set a class', function () {
	expect($this->button->setClass('some_class')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['class' => 'some_class']));
});

it('can set a style', function () {
	expect($this->button->setStyle('display: hidden')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['style' => 'display: hidden']));
});

it('can return a name', function () {
	expect($this->button->getName())->toBe('submit');
});

it('can return a value', function () {
	expect($this->button->getValue())->toBe('Submit');
});

it('can return a type', function () {
	expect($this->button->getType())->toBe('button');
});

it('returns all attributes as an array', function () {
	$attributes = [
		'type'  => 'reset',
		'class' => 'some_class',
		'style' => 'display: none',
	];

	$button = $this->button->setAttributes($attributes);

	expect($button->toArray())->toMatchArray(array_merge($this->resultArray, $attributes));
});

<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Field;

beforeEach(function () {
	$this->field = Field::make('username', 'Your name');

	$this->resultArray = [
		'name'  => 'username',
		'label' => 'Your name',
		'id'    => 'username',
	];
});

it('can return a name of the field', function () {
	expect($this->field->getName())->toBe('username');
});

it('can return value', function () {
	expect($this->field->setValue('foo')->getValue())->toBe('foo');
});

it('can set a type of the field', function () {
	expect($this->field->setType('text')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['type' => 'text']));
});

it('can set a value of the field', function () {
	expect($this->field->setValue('John')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['value' => 'John']));
});

it('can set a class of the field', function () {
	expect($this->field->setClass('form-control')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['class' => 'form-control']));
});

it('can set a style of the field', function () {
	expect($this->field->setStyle('width: 5%')->toArray())
		->toMatchArray(array_merge($this->resultArray, ['style' => 'width: 5%']));
});

it('can set required', function () {
	expect($this->field->required()->toArray()['required'])->toBeTrue();
});

it('can set readonly', function () {
	expect($this->field->readonly()->toArray()['readonly'])->toBeTrue();
});

it('can set disabled', function () {
	expect($this->field->disabled()->toArray()['disabled'])->toBeTrue();
});

it('returns all attributes as an array', function () {
	expect($this->field->toArray())->toMatchArray($this->resultArray)
		->and($this->field->toArray())->toBeArray();
});

it('can be filled via setAttributes method', function () {
	$attributes = [
		'name'  => 'foo',
		'label' => 'Jerry',
		'id'    => 'fas',
		'class' => 'form-control',
		'style' => 'width: 5%',
	];
	$this->field->setAttributes($attributes);

	expect($this->field->toArray())->toMatchArray($attributes);
});

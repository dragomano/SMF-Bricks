<?php declare(strict_types=1);

use Bugo\Bricks\Forms\CheckboxField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = CheckboxField::make('checkbox_field', 'Check me please')
		->checked();
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::CHECKBOX->value);
});

it('can set a value', function () {
	expect($this->field->setValue('foo')->getValue())->toBe('foo');
});

it('can be checked/unchecked', function () {
	expect($this->field->toArray()['checked'])->toBeTrue();

	$this->field->checked(false);

	expect($this->field->toArray()['checked'])->toBeFalse();
});

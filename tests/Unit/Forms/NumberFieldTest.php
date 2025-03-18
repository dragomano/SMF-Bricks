<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Interfaces\MinMaxStepInterface;
use Bugo\Bricks\Forms\NumberField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = NumberField::make('number_field', 'Enter any number');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::NUMBER->value);
});

it('implements some interfaces', function () {
	expect(NumberField::class)->toImplement(MinMaxStepInterface::class);
});

it('can set a value', function () {
	expect($this->field->setValue(123)->getValue())->toBe(123);
});

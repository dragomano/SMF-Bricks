<?php declare(strict_types=1);

use Bugo\Bricks\Forms\RangeField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = RangeField::make('range_field', 'Setup a range')
		->setMin(1)
		->setMax(100)
		->setStep(5);
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::RANGE->value);
});

it('can set a value', function () {
	expect($this->field->setValue(20)->getValue())->toBe(20);
});

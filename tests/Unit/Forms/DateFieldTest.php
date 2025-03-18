<?php declare(strict_types=1);

use Bugo\Bricks\Forms\DateField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;
use Bugo\Bricks\Forms\Interfaces\MinMaxStepInterface;

beforeEach(function () {
	$this->field = DateField::make('date_field', 'Enter your date');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::DATE->value);
});

it('can set a value', function () {
	expect($this->field->setValue(date('Y-m-d'))->getValue())->toBe(date('Y-m-d'));
});

it('implements some interfaces', function () {
	expect(DateField::class)->toImplement(MinMaxStepInterface::class);
});

<?php declare(strict_types=1);

use Bugo\Bricks\Forms\TelField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = TelField::make('tel_field', 'Enter your phone number')
		->setPattern('[0-9]{3}-[0-9]{3}-[0-9]{4}');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::TEL->value);
});

it('can set a value', function () {
	expect($this->field->setValue('123-456-7890')->getValue())->toBe('123-456-7890');
});

<?php declare(strict_types=1);

use Bugo\Bricks\Forms\ColorField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = ColorField::make('color_field', 'Choose your color');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::COLOR->value);
});

it('can set a value', function () {
	expect($this->field->setValue('#123456')->getValue())->toBe('#123456');
});

it('can filter input', function () {
	expect($this->field->setValue('red')->getValue())->toBe('');
});

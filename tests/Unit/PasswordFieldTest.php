<?php declare(strict_types=1);

use Bugo\Bricks\Forms\PasswordField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = PasswordField::make('password_field', 'Enter a password');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::PASSWORD->value);
});

it('can set a value', function () {
	expect($this->field->setValue('pass')->getValue())->toBe('pass');
});

it('can have autocomplete', function () {
	expect($this->field->setAutocomplete('off')->toArray()['autocomplete'])->toBe('off');
});

it('can have input mode', function () {
	expect($this->field->setInputMode('numeric')->toArray()['inputmode'])->toBe('numeric');
});

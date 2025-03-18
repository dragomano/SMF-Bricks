<?php declare(strict_types=1);

use Bugo\Bricks\Forms\SearchField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = SearchField::make('search_field', 'Make your choice');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::SEARCH->value);
});

it('can set a value', function () {
	expect($this->field->setValue('how to make a sandwich')->getValue())->toBe('how to make a sandwich');
});

it('can have autosave', function () {
	expect($this->field->autosave()->toArray()['autosave'])->toBeTrue();
});

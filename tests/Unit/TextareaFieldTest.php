<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;
use Bugo\Bricks\Forms\TextareaField;

beforeEach(function () {
	$this->field = TextareaField::make('textarea_field', 'Your text here')
		->setPlaceholder('Write something about yourself')
		->setMaxLength(1000)
		->setCols(10)
		->setRows(4);
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::TEXTAREA->value);
});

it('can set a value', function () {
	expect($this->field->setValue('lorem ipsum')->getValue())->toBe('lorem ipsum');
});

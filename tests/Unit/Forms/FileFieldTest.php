<?php declare(strict_types=1);

use Bugo\Bricks\Forms\FileField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = FileField::make('file_field', 'Upload some file');
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::FILE->value);
});

it('has accept method', function () {
	expect($this->field->accept('image/png, image/jpeg')->toArray())->toHaveKey('accept');
});

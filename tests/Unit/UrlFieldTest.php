<?php declare(strict_types=1);

use Bugo\Bricks\Forms\UrlField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = UrlField::make('url_field', 'Your site URL')
		->setSize(80);
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::URL->value);
});

it('can set a value', function () {
	expect($this->field->setValue('https://simplemachines.org')->getValue())
		->toBe('https://simplemachines.org');
});

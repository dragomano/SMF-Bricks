<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\RadioField;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;

beforeEach(function () {
	$this->field = RadioField::make('radio_field', 'Make your choice')
		->setOptions([
			'no' => 'No',
			'yes' => 'Yes',
		]);
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::RADIO->value);
});

it('implements some interfaces', function () {
	expect(RadioField::class)->toImplement(OptionProviderInterface::class)
		->and(RadioField::class)->toImplement(SelectableInterface::class);
});

it('can set a value', function () {
	expect($this->field->setValue('no')->getValue())->toBe('no');
});


it('can return options', function () {
	expect($this->field->getOptions())->toMatchArray([
		'no' => 'No',
		'yes' => 'Yes',
	]);
});

<?php declare(strict_types=1);

use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\HtmlFieldType;
use Bugo\Bricks\Forms\Interfaces\OptionProviderInterface;
use Bugo\Bricks\Forms\Interfaces\SelectableInterface;
use Bugo\Bricks\Forms\Interfaces\SizeableInterface;
use Bugo\Bricks\Forms\SelectField;

beforeEach(function () {
	$this->field = SelectField::make('select_field', 'Make your choice')
		->setValue('yes')
		->setOptions([
			'no' => 'No',
			'yes' => 'Yes',
		]);
});

it('is an instance of Field class', function () {
	expect($this->field)->toBeInstanceOf(Field::class)
		->and($this->field->getType())->toBe(HtmlFieldType::SELECT->value);
});

it('implements some interfaces', function () {
	expect(SelectField::class)->toImplement(OptionProviderInterface::class)
		->and(SelectField::class)->toImplement(SelectableInterface::class)
		->and(SelectField::class)->toImplement(SizeableInterface::class);
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

it('can be multiple', function () {
	expect($this->field->multiple()->setValue(['no', 'yes'])->getValue())->toBe(['no', 'yes'])
		->and($this->field->multiple()->setValue('test')->getValue())->toBe(['test']);
});

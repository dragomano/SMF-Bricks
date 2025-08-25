<?php declare(strict_types=1);

use Bugo\Bricks\Forms\EmailField;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\Field;
use Bugo\Bricks\Forms\ResetButton;
use Bugo\Bricks\Forms\SubmitButton;
use Bugo\Bricks\Forms\TextareaField;
use Bugo\Bricks\Forms\TextField;

beforeEach(function () {
	$this->builder = FormBuilder::make('form_id', 'Test Form')->name('my_form');
});

it('can validate fields on adding', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addFields([
		['array instead of Field class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can add fields to the form', function () {
	$this->builder->addFields([
		Field::make('username', 'Username'),
		TextField::make('bio', 'Bio')
			->setMinLength(100)
			->setMaxLength(1000),
		EmailField::make(),
	]);

	expect($this->builder->build()['fields'])->toHaveCount(3);
});

it('can add fields within a container', function () {
	$this->builder->addFields([
		Field::make('username', 'Username'),
		TextField::make('bio', 'Bio')
			->setMinLength(100)
			->setMaxLength(1000),
		EmailField::make(),
	], 'container_class');

	expect($this->builder->build()['fields'][0]['type'])->toBe('container');
});

it('can add a field to the form', function () {
	$field = Field::make('username', 'Username');
	$this->builder->addField($field);

	expect($this->builder->build()['fields'][$field->getName()])->toBe($field);
});

it('can remove a field from the form', function () {
	$field = TextareaField::make('description', 'Description');
	$this->builder->addField($field);

	expect($this->builder->removeField($field->getName())->build()['fields'])->not->toContain($field->getName());
});

it('can validate buttons on adding', function () {
	/** @noinspection PhpParamsInspection */
	expect($this->builder->addButtons([
		['array instead of Button class'],
	]))->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('can add buttons to the form', function () {
	$this->builder->addButtons([
		SubmitButton::make(),
		ResetButton::make(),
	]);

	expect($this->builder->build()['buttons'])->toHaveCount(2);
});

it('can add buttons within a container', function () {
	$this->builder->addButtons([
		SubmitButton::make(),
		ResetButton::make(),
	], 'container_class');

	expect($this->builder->build()['buttons'][0]['type'])->toBe('container');
});

it('can add a button to the form', function () {
	$button = SubmitButton::make();
	$this->builder->addButton($button);

	expect($this->builder->build()['buttons'][$button->getName()])->toBe($button);
});

it('can remove a button from the form', function () {
	$button = SubmitButton::make();
	$this->builder->addButton($button);

	expect($this->builder->removeButton($button->getName())->build()['buttons'])->not->toContain($button->getName());
});

it('can add hidden fields to the form', function () {
	$this->builder->addHiddenFields(['session_id' => '123456']);

	expect($this->builder->build()['fields'])->toHaveCount(1);
});

it('can set form options', function () {
	$this->builder->withParams('/submit', 'POST', true, 'some_class', '_blank');

	$formData = $this->builder->build();
	expect($formData['action'])->toBe('/submit')
		->and($formData['method'])->toBe('POST')
		->and($formData['enctype'])->toBe('multipart/form-data')
		->and($formData['class'])->toBe('some_class')
		->and($formData['target'])->toBe('_blank');
});

it('can set form style', function () {
	$this->builder->setStyle('display: none');

	expect($this->builder->build()['style'])->toBe('display: none');
});

it('can set javascript', function () {
	$this->builder->setScript('console.log("Hello!")');

	expect($this->builder->build()['script'])->toBe('console.log("Hello!")');

	$this->builder->removeScript();

	expect($this->builder->build())->not->toHaveKey('script');
});

it('can set body class', function () {
	$this->builder->setBodyClass('form-body');

	expect($this->builder->build()['body_class'])->toBe('form-body');
});

it('can return id', function () {
	expect($this->builder->getId())->toBe('form_id');
});

it('can return title', function () {
	expect($this->builder->getTitle())->toBe('Test Form');
});

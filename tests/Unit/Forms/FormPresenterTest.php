<?php declare(strict_types=1);

use Bugo\Bricks\Forms\CheckboxField;
use Bugo\Bricks\Forms\EmailField;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\FormPresenter;
use Bugo\Bricks\Forms\FormRenderer;
use Bugo\Bricks\Forms\RadioField;
use Bugo\Bricks\Forms\ResetButton;
use Bugo\Bricks\Forms\SelectField;
use Bugo\Bricks\Forms\SubmitButton;
use Bugo\Bricks\Forms\TextareaField;
use Bugo\Bricks\Forms\TextField;

beforeEach(function () {
	$_SERVER['REQUEST_METHOD'] = 'POST';

	$renderer = new FormRenderer();
	$this->presenter = new FormPresenter($renderer);

	$this->builder = FormBuilder::make('form_id', 'Test Form')
		->addFields([
			TextField::make('username', 'Username'),
		], 'container')
		->addFields([
			EmailField::make(),
			CheckboxField::make('check', 'Check me'),
			TextareaField::make('textarea', 'Message'),
			SelectField::make('select', 'Make your choice')
				->setValue('yes')
				->setOptions([
					'no' => 'No',
					'yes' => 'Yes',
				]),
			RadioField::make('radio', 'Are you ready?')
				->setOptions([
					'no' => 'No',
					'yes' => 'Yes'
				]),
		])
		->addButtons([
			ResetButton::make(),
		], 'container')
		->addButtons([
			SubmitButton::make(),
		])
		->setScript('console.log("Hello!")')
		->addHiddenFields(['session_id' => '123456']);
});

it('renders the form correctly', function () {
	ob_start();
	$this->presenter->show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test Form')
		->and($output)->toContain('<input name="username" id="username" type="text">')
		->and($output)->toContain('<input name="email" id="email" type="email">')
		->and($output)->toContain('<input name="session_id" id="session_id" type="hidden" value="123456">')
		->and($output)->toContain('<input name="submit" value="Submit" type="submit">')
		->and($output)->toContain('<script>console.log("Hello!")</script>');
});

it('renders the form correctly with $_GET query', function () {
	$_SERVER['REQUEST_METHOD'] = 'GET';

	ob_start();
	$this->presenter->show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test Form')
		->and($output)->toContain('<input name="username" id="username" type="text">')
		->and($output)->toContain('<input name="email" id="email" type="email">')
		->and($output)->toContain('<input name="session_id" id="session_id" type="hidden" value="123456">')
		->and($output)->toContain('<input name="submit" value="Submit" type="submit">');
});

it('renders the form without buttons', function () {
	$builder = FormBuilder::make('form_id', 'Test Form')
		->addFields([
			TextField::make('username', 'Username'),
		]);

	ob_start();
	$this->presenter->show($builder);
	$output = ob_get_clean();

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test Form')
		->and($output)->toContain('name="username"');
});

it('throws InvalidArgumentException when RadioField options are missing', function () {
	$builder = FormBuilder::make('form_id', 'Test Form')
		->addFields([
			RadioField::make('radio_field', 'Make your choice'),
		]);

	ob_end_flush();

	ob_start();
	$this->presenter->show($builder);
	$output = ob_get_clean();

	expect($output)->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('throws InvalidArgumentException when SelectField options are missing', function () {
	$builder = FormBuilder::make('form_id', 'Test Form')
		->addFields([
			SelectField::make('select_field', 'Make your choice'),
		]);

	ob_end_flush();

	ob_start();
	$this->presenter->show($builder);
	$output = ob_get_clean();

	expect($output)->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('renders the form correctly with filled $_POST data', function () {
	$_POST['username'] = 'John Doe';
	$_POST['email'] = 'john@doe.com';
	$_POST['check'] = 'on';
	$_POST['textarea'] = 'foo bar';
	$_POST['select'] = 'no';
	$_POST['radio'] = 'yes';

	ob_start();
	$this->presenter->show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('<input name="username" id="username" type="text" value="John Doe">')
		->and($output)->toContain('<input name="email" id="email" type="email" value="john&#64;doe.com">')
		->and($output)->toContain('<input name="check" id="check" type="checkbox" checked value="on">')
		->and($output)->toContain('<textarea name="textarea" id="textarea">foo bar</textarea>')
		->and($output)->toContain('<select name="select" id="select"><option value="no" selected>No</option><option value="yes">Yes</option></select>')
		->and($output)->toContain('<input name="radio" value="no" type="radio"><input name="radio" value="yes" type="radio" checked>');
});

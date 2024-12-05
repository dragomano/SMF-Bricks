<?php declare(strict_types=1);

use Bugo\Bricks\Forms\CheckboxField;
use Bugo\Bricks\Forms\EmailField;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\RadioField;
use Bugo\Bricks\Forms\ResetButton;
use Bugo\Bricks\Forms\SelectField;
use Bugo\Bricks\Forms\SubmitButton;
use Bugo\Bricks\Forms\TextareaField;
use Bugo\Bricks\Forms\TextField;
use Bugo\Bricks\Presenters\FormPresenter;

beforeEach(function () {
	$_SERVER['REQUEST_METHOD'] = 'POST';

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
		->addHiddenFields(['session_id' => '123456']);
});

it('renders the form correctly', function () {
	ob_start();
	FormPresenter::show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test Form')
		->and($output)->toContain('name="username"')
		->and($output)->toContain('name="email"')
		->and($output)->toContain('name="session_id"')
		->and($output)->toContain('value="123456"')
		->and($output)->toContain('name="submit"');
});

it('renders the form correctly with $_GET query', function () {
	$_SERVER['REQUEST_METHOD'] = 'GET';

	ob_start();
	FormPresenter::show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test Form')
		->and($output)->toContain('name="username"')
		->and($output)->toContain('name="email"')
		->and($output)->toContain('name="session_id"')
		->and($output)->toContain('value="123456"')
		->and($output)->toContain('name="submit"');
});

it('renders the form without buttons', function () {
	$builder = FormBuilder::make('form_id', 'Test Form')
		->addFields([
			TextField::make('username', 'Username'),
		]);

	ob_start();
	FormPresenter::show($builder);
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
	FormPresenter::show($builder);
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
	FormPresenter::show($builder);
	$output = ob_get_clean();

	expect($output)->toThrow(InvalidArgumentException::class);
})->throws(InvalidArgumentException::class);

it('renders the form correctly with filled $_POST data', function () {
	$_POST['check'] = 'on';
	$_POST['textarea'] = 'foo bar';
	$_POST['select'] = 'no';
	$_POST['radio'] = 'yes';

	ob_start();
	FormPresenter::show($this->builder);
	$output = ob_get_clean();

	expect($output)->toContain('<input name="check" id="check" type="checkbox" checked value="on">')
		->and($output)->toContain('<textarea name="textarea" id="textarea" type="textarea">foo bar</textarea>')
		->and($output)->toContain('<select name="select" id="select" type="select"><option value="no" selected>No</option><option value="yes">Yes</option></select>')
		->and($output)->toContain('<input value="no" type="radio" name="radio"><input value="yes" type="radio" name="radio" checked>');
});

<?php declare(strict_types=1);

use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\FormPresenter;
use Bugo\Bricks\Forms\FormRenderer;
use Bugo\Bricks\Forms\RadioField;
use Bugo\Bricks\Forms\TextField;
use Tests\Unit\Forms\CustomButton;
use Tests\Unit\Forms\CustomField;

beforeEach(function () {
	// FormRenderer reads $_POST and $_SERVER directly,
	// so these tests must not depend on the state left over by other test files.
	$_POST = [];

	$_SERVER['REQUEST_METHOD'] = 'GET';
});

/**
 * Renders the form and turns every PHP warning into an exception,
 * so that missing array keys are not silently ignored.
 */
function renderFormQuietly(callable $callback): string
{
	set_error_handler(static function (int $severity, string $message): bool {
		throw new ErrorException($message);
	});

	try {
		ob_start();

		$callback();

		return (string) ob_get_clean();
	} finally {
		restore_error_handler();
	}
}

it('renders a form without any fields', function () {
	$output = renderFormQuietly(static function () {
		(new FormRenderer())->render(FormBuilder::make('form_id', 'Test form')->build());
	});

	expect($output)->toContain('id="form_id"')
		->and($output)->toContain('Test form');
});

it('renders a custom FieldInterface implementation', function () {
	$builder = FormBuilder::make('form_id', 'Test form')
		->addFields([CustomField::make('custom_field', 'Custom field')]);

	$output = renderFormQuietly(static function () use ($builder) {
		(new FormPresenter(new FormRenderer()))->show($builder);
	});

	expect($output)->toContain('<input name="custom_field" type="text" id="custom_field">')
		->and($output)->toContain('for="custom_field"')
		->and($output)->toContain('Custom field');
});

it('renders a custom ButtonInterface implementation', function () {
	$builder = FormBuilder::make('form_id', 'Test form')
		->addFields([TextField::make('username', 'Username')])
		->addButtons([CustomButton::make('custom_button', 'Press me')]);

	$output = renderFormQuietly(static function () use ($builder) {
		(new FormPresenter(new FormRenderer()))->show($builder);
	});

	expect($output)->toContain('<input name="custom_button" value="Press me" type="button">');
});

it('points the label to the field id', function () {
	$builder = FormBuilder::make('form_id', 'Test form')
		->addFields([TextField::make('username', 'Username')->setId('custom-id')]);

	$output = renderFormQuietly(static function () use ($builder) {
		(new FormPresenter(new FormRenderer()))->show($builder);
	});

	expect($output)->toContain('for="custom-id"')
		->and($output)->toContain('<input name="username" id="custom-id" type="text">');
});

it('wraps radio options into labels', function () {
	$builder = FormBuilder::make('form_id', 'Test form')
		->addFields([
			RadioField::make('radio', 'Are you ready?')->setOptions([
				'no'  => 'No',
				'yes' => 'Yes',
			]),
		]);

	$output = renderFormQuietly(static function () use ($builder) {
		(new FormPresenter(new FormRenderer()))->show($builder);
	});

	expect($output)->toContain('<label><input name="radio" value="no" type="radio"> No</label>')
		->and($output)->toContain('<label><input name="radio" value="yes" type="radio"> Yes</label>');
});

it('throws for an invalid field payload', function () {
	expect(static fn() => (new FormRenderer())->render(['id' => 'form_id', 'title' => '', 'fields' => ['bogus']]))
		->toThrow(InvalidArgumentException::class, 'Field must be an instance of');
});

it('throws for an invalid button payload', function () {
	expect(static fn() => (new FormRenderer())->render(['id' => 'form_id', 'title' => '', 'buttons' => ['bogus']]))
		->toThrow(InvalidArgumentException::class, 'Button must be an instance of');
});

it('throws for an invalid container element', function () {
	$data = [
		'id'     => 'form_id',
		'title'  => '',
		'fields' => [
			['type' => 'container', 'class' => 'some_class', 'fields' => ['bogus']],
		],
	];

	expect(static fn() => (new FormRenderer())->render($data))
		->toThrow(InvalidArgumentException::class, 'Container element must be an instance of');
});

it('throws for button data without the name key', function () {
	$button = new class extends CustomButton {
		public function toArray(): array
		{
			return ['value' => 'No name'];
		}
	};

	$builder = FormBuilder::make('form_id', 'Test form')->addButtons([$button]);

	expect(static fn() => (new FormPresenter(new FormRenderer()))->show($builder))
		->toThrow(InvalidArgumentException::class, 'The button data must contain the "name" key');
});

it('renders the style and target attributes of the form', function () {
	$builder = FormBuilder::make('ads_block_form')
		->setAction('https://example.com/index.php?action=admin')
		->setStyle('display: none')
		->setTarget('_blank')
		->addHiddenFields(['add_block' => 'ads_block']);

	$output = renderFormQuietly(static function () use ($builder) {
		(new FormPresenter(new FormRenderer()))->show($builder);
	});

	expect($output)->toContain('style="display: none"')
		->and($output)->toContain('target="_blank"')
		->and($output)->toContain('<input name="add_block" id="add_block" type="hidden" value="ads_block">');
});

it('throws for field data without the type and name keys', function () {
	$field = new class extends CustomField {
		public function toArray(): array
		{
			return [];
		}
	};

	$builder = FormBuilder::make('form_id', 'Test form')->addFields([$field]);

	expect(static fn() => (new FormPresenter(new FormRenderer()))->show($builder))
		->toThrow(InvalidArgumentException::class, 'The field data must contain the "type" and "name" keys');
});

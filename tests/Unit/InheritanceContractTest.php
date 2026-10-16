<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\InputField;
use Bugo\Bricks\Forms\ResetButton;
use Bugo\Bricks\Forms\SubmitButton;
use Bugo\Bricks\Settings\ConfigBuilder;
use Bugo\Bricks\Settings\TextConfig;
use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\Tables\TableBuilder;

it('lets form subclasses extend construction and normalize identity through setters', function () {
	$builder = new class('form', 'Title') extends FormBuilder {
		public function __construct(string $id, string $title = '')
		{
			parent::__construct($id, $title);
		}

		public function setId(string $id): static
		{
			return parent::setId('custom_' . $id);
		}

		public function setTitle(string $title): static
		{
			return parent::setTitle('Custom ' . $title);
		}
	};

	$fromFactory = $builder::make('factory', 'Factory');

	expect($builder->getId())->toBe('custom_form')->and($builder->getTitle())->toBe('Custom Title')
		->and($fromFactory)->toBeInstanceOf($builder::class)
		->and($fromFactory->getId())->toBe('custom_factory')
		->and($fromFactory->getTitle())->toBe('Custom Factory');
});

it('lets table subclasses normalize identity during construction', function () {
	$builder = new class('table', 'Title') extends TableBuilder {
		public function __construct(string $id, string $title)
		{
			parent::__construct($id, $title);
		}

		public function setId(string $id): static
		{
			return parent::setId('custom_' . $id);
		}

		public function setTitle(string $title): static
		{
			return parent::setTitle('Custom ' . $title);
		}
	};

	$fromFactory = $builder::make('factory', 'Factory');

	expect($builder->getId())->toBe('custom_table')->and($builder->getTitle())->toBe('Custom Title')
		->and($fromFactory)->toBeInstanceOf($builder::class)
		->and($fromFactory->getId())->toBe('custom_factory');
});

it('lets breadcrumb builder subclasses call the parent constructor', function () {
	$builder = new class extends BreadcrumbBuilder {
		public function __construct()
		{
			parent::__construct();
		}
	};

	$fromFactory = $builder::make();

	expect($fromFactory)->toBeInstanceOf($builder::class)
		->and($fromFactory->add('Home', '/')->build())->toBe([['name' => 'Home', 'url' => '/']]);
});

it('lets config builder subclasses call the parent constructor', function () {
	$builder = new class extends ConfigBuilder {
		public function __construct()
		{
			parent::__construct();
		}
	};

	$fromFactory = $builder::make();

	expect($fromFactory)->toBeInstanceOf($builder::class)
		->and($fromFactory->addVar(TextConfig::make('setting'))->build()[0])->toMatchArray([0 => 'text', 1 => 'setting']);
});

it('lets row subclasses call the parent constructor', function () {
	$row = new class('Row', 'row-class') extends Row {
		public function __construct(string $value, string $class = '')
		{
			parent::__construct($value, $class);
		}
	};

	$fromFactory = $row::make('Factory row', 'factory-class');

	expect($fromFactory)->toBeInstanceOf($row::class)
		->and($fromFactory->toArray())->toMatchArray(['value' => 'Factory row', 'class' => 'factory-class']);
});

it('lets reset button subclasses call the parent constructor', function () {
	$button = new class('reset', 'Reset') extends ResetButton {
		public function __construct(string $name, string $value)
		{
			parent::__construct($name, $value);
		}
	};

	$fromFactory = $button::make('cancel', 'Cancel');

	expect($fromFactory)->toBeInstanceOf($button::class)
		->and($fromFactory->toArray())->toMatchArray(['name' => 'cancel', 'value' => 'Cancel', 'type' => 'reset']);
});

it('lets submit button subclasses call the parent constructor', function () {
	$button = new class('submit', 'Submit') extends SubmitButton {
		public function __construct(string $name, string $value)
		{
			parent::__construct($name, $value);
		}
	};

	$fromFactory = $button::make('save', 'Save');

	expect($fromFactory)->toBeInstanceOf($button::class)
		->and($fromFactory->toArray())->toMatchArray(['name' => 'save', 'value' => 'Save', 'type' => 'submit']);
});

it('provides input defaults to third-party input subclasses', function () {
	$field = new class('custom', 'Custom input') extends InputField {
		public function __construct(string $name, string $label)
		{
			parent::__construct($name, $label);
		}
	};

	expect($field->toArray())->toBe(['name' => 'custom', 'label' => 'Custom input', 'id' => 'custom', 'type' => 'input']);
});

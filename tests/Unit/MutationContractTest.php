<?php declare(strict_types=1);

use Bugo\Bricks\Breadcrumbs\BreadcrumbBuilder;
use Bugo\Bricks\Breadcrumbs\BreadcrumbPresenter;
use Bugo\Bricks\Forms\Button;
use Bugo\Bricks\Forms\CheckboxField;
use Bugo\Bricks\Forms\FileField;
use Bugo\Bricks\Forms\FormBuilder;
use Bugo\Bricks\Forms\FormRenderer;
use Bugo\Bricks\Forms\HtmlFieldType;
use Bugo\Bricks\Forms\RadioField;
use Bugo\Bricks\Forms\RangeField;
use Bugo\Bricks\Forms\RequestData;
use Bugo\Bricks\Forms\ResetButton;
use Bugo\Bricks\Forms\SelectField;
use Bugo\Bricks\Forms\TextField;
use Bugo\Bricks\StringRendererInterface;
use Bugo\Bricks\Settings\CallbackConfig;
use Bugo\Bricks\Settings\FloatConfig;
use Bugo\Bricks\Settings\IntConfig;
use Bugo\Bricks\Settings\SelectConfig;
use Bugo\Bricks\Settings\TextConfig;
use Bugo\Bricks\Tables\Column;
use Bugo\Bricks\Tables\TableBuilder;
use Bugo\Bricks\Tables\TableRenderer;
use Nette\Utils\Html;

it('keeps the name label id and type of every concrete field', function (string $class, string $type) {
	expect($class::make('field_name', 'Field label')->toArray())->toMatchArray([
		'name' => 'field_name', 'label' => 'Field label', 'id' => 'field_name', 'type' => $type,
	]);
})->with(array_map(static fn(string $type): array => ['Bugo\\Bricks\\Forms\\' . ucfirst($type) . 'Field', $type], [
	'checkbox', 'color', 'date', 'email', 'file', 'hidden', 'number', 'password', 'radio',
	'range', 'search', 'select', 'tel', 'text', 'textarea', 'url',
]));

it('keeps default form and table parameters', function () {
	expect(FormBuilder::make('form')->build())->toBe(['id' => 'form', 'title' => '', 'method' => 'post'])
		->and(FormBuilder::make('form')->setMethod('get')->build()['method'])->toBe('get')
		->and(TableBuilder::make('table', 'Table')->build())->toBe(['id' => 'table', 'title' => 'Table', 'items_per_page' => 15])
		->and(RangeField::make('range', 'Range')->setMin()->setMax()->setStep()->toArray())
		->toMatchArray(['min' => 0, 'max' => 10, 'step' => 20])
		->and(FileField::make('files', 'Files')->multiple()->toArray()['multiple'])->toBeTrue()
		->and(ResetButton::make()->toArray())->toMatchArray(['name' => 'reset', 'value' => 'Reset', 'type' => 'reset']);
});

it('keeps request snapshots isolated between renderer instances', function () {
	$data   = FormBuilder::make('form')->addField(TextField::make('text', 'Text'))->build();
	$first  = (new FormRenderer())->withRequest(new RequestData('POST', ['text' => 'first']));
	$second = $first->withRequest(new RequestData('POST', ['text' => 'second']));

	expect($first->render($data))->toContain('value="first"')
		->and($second->render($data))->toContain('value="second"');
});

it('renders the full form structure with multiple buttons and containers', function () {
	$data = FormBuilder::make('form', 'Title')
		->setName('custom_name')->setBodyClass('body-class')
		->addHiddenFields(['token' => 'value'])
		->addFields([TextField::make('text', 'Text')], 'field-container')
		->addButton(Button::make('first', 'First')->setId('first-button'))
		->addButton(Button::make('second', 'Second'))
		->addButtons([Button::make('third', 'Third')], 'button-container')
		->build();

	$html = (new FormRenderer())->render($data);

	expect($html)->toContain('accept-charset="utf-8"', 'name="custom_name"', '<div class="cat_bar"><h3 class="catbg">Title</h3></div>')
		->toContain('<div class="body-class">', '<div class="field-container"><dl class="settings">')
		->toContain('<div class="button-container"><input name="third" value="Third" type="button"></div>')
		->toContain('<input name="token" id="token" type="hidden" value="value">')
		->not->toContain('for="token"')
		->and(substr_count($html, 'name="first"'))->toBe(1)
		->and($html)->toContain('id="first-button"', 'name="second"');
});

it('clears an unchecked checkbox on POST', function () {
	$data = FormBuilder::make('form')
		->addField(CheckboxField::make('enabled', 'Enabled')->setAttributes(['checked' => true]))->build();

	$html = (new FormRenderer())->withRequest(new RequestData('POST'))->render($data);

	expect($html)->toContain('name="enabled"')->not->toContain('checked');
});

it('keeps numeric option keys unselected until a value is provided', function () {
	$data = FormBuilder::make('form')->addFields([
		RadioField::make('radio', 'Radio')->setOptions([0 => 'Zero']),
		SelectField::make('select', 'Select')->setOptions([0 => 'Zero']),
	])->build();

	$html = (new FormRenderer())->render($data);

	expect($html)->toContain('<select name="select"', 'value="0"')
		->not->toContain('checked', 'selected');
});

it('rejects empty and mismatched form containers', function () {
	$renderer = new FormRenderer();

	expect(fn() => $renderer->render(['id' => 'form', 'title' => '', 'fields' => [[]]]))
		->toThrow(InvalidArgumentException::class)
		->and(fn() => $renderer->render(['id' => 'form', 'title' => '', 'buttons' => [[]]]))
		->toThrow(InvalidArgumentException::class)
		->and(fn() => $renderer->render([
			'id' => 'form', 'title' => '', 'fields' => [['fields' => [Button::make('button', 'Button')]]],
		]))->toThrow(InvalidArgumentException::class, 'Container element must be an instance of Bugo\\Bricks\\Forms\\Interfaces\\FieldInterface')
		->and(fn() => $renderer->render([
			'id' => 'form', 'title' => '', 'buttons' => [['buttons' => [TextField::make('text', 'Text')]]],
		]))->toThrow(InvalidArgumentException::class, 'Container element must be an instance of Bugo\\Bricks\\Forms\\Interfaces\\ButtonInterface');
});

it('reindexes breadcrumbs after removing an item', function () {
	$builder = BreadcrumbBuilder::make()->add('First')->add('Second')->add('Third')->remove(0);

	expect($builder->getByIndex(0)['name'])->toBe('Second')
		->and($builder->getByIndex(1)['name'])->toBe('Third');
});

it('normalizes numeric breadcrumb decorations and ignores unknown update keys', function () {
	$builder = BreadcrumbBuilder::make()->add('Home')->update(0, 'before', 0)->update(0, 'after', 42);

	expect($builder->build()[0])->toMatchArray(['before' => '0', 'after' => '42'])
		->and($builder->update(0, 'unknown', []))->toBe($builder);
});

it('calls a string renderer once when presenting output', function () {
	$renderer = new class implements StringRendererInterface {
		public int $calls = 0;

		public function render(array $data): string
		{
			$this->calls++;
			return 'output';
		}
	};

	ob_start();

	try {
		(new BreadcrumbPresenter($renderer))->show(BreadcrumbBuilder::make()->add('Home'));
		$output = ob_get_contents();
	} finally {
		ob_end_clean();
	}

	expect($output)->toBe('output')->and($renderer->calls)->toBe(1);
});

it('keeps table styles custom sort and accumulated hidden fields', function () {
	$data = TableBuilder::make('table', 'Title')
		->addHiddenFields(['first' => 'one'])->addHiddenFields(['second' => 'two'])
		->setCount('12')->addColumn(Column::make('name', 'Name')->setSort('name', 'name ASC'))->build();

	expect($data['form']['hidden_fields'])->toBe(['first' => 'one', 'second' => 'two'])
		->and($data['get_count']['value'])->toBe(12)
		->and($data['columns']['name']['sort']['reverse'])->toBe('name ASC');

	$html = (new TableRenderer())->render([
		'id' => 'table', 'title' => 'Title', 'headers' => [['id' => 'name', 'label' => 'Name']],
		'rows' => [['data' => [['value' => 'Cell']]]],
	]);

	expect($html)->toContain('<h3 class="catbg">Title</h3>', '<th id="name">Name</th>', '<tr class="windowbg"><td>Cell</td></tr>');
});

it('exposes input classification for every HTML field type', function () {
	foreach (HtmlFieldType::cases() as $type) {
		$isInput = ! in_array($type->value, ['button', 'reset', 'select', 'submit', 'textarea'], true);
		expect($type->isInput())->toBe($isInput);
	}
});

it('keeps radio values selected before any POST request', function () {
	$field = RadioField::make('choice', 'Choice')->setOptions(['yes' => 'Yes', 'no' => 'No'])->setValue('no');
	$html  = (new FormRenderer())->withRequest(new RequestData())->render(FormBuilder::make('form')->addField($field)->build());

	expect($field->toArray()['selected'])->toBe('no')
		->and($html)->toContain('value="no" type="radio" checked')
		->not->toContain('value="yes" type="radio" checked');
});

it('ignores supplied POST data when the request method is GET', function () {
	$data = FormBuilder::make('form')->addField(TextField::make('text', 'Text')->setValue('original'))->build();
	$html = (new FormRenderer())->withRequest(new RequestData('GET', ['text' => 'stale']))->render($data);

	expect($html)->toContain('value="original"')->not->toContain('value="stale"');
});

it('escapes HTML objects when table escaping is enabled', function () {
	$html = Html::el('strong')->setText('A & B');
	$data = ['id' => 'table', 'rows' => [['data' => [['value' => $html]]]]];

	expect((new TableRenderer())->render($data))->toContain('<strong>A &amp; B</strong>')
		->and((new TableRenderer(escapeData: true))->render($data))
		->toContain('&lt;strong&gt;A &amp;amp; B&lt;/strong&gt;')->not->toContain('<strong>');
});

it('keeps public numeric config extensions available to consumers', function () {
	$integer = IntConfig::make('integer')->setMin(0)->setMax(20)->setStep(3);
	$float   = FloatConfig::make('float')->setMin(0.0)->setMax(2.5)->setStep(0.25);

	expect($integer->extendData())->toBe(['min' => 0, 'max' => 20, 'step' => 3])
		->and($float->extendData())->toBe(['min' => 0.0, 'max' => 2.5, 'step' => 0.25])
		->and(IntConfig::make('default')->extendData())->toBe(['min' => null, 'max' => null, 'step' => 1])
		->and(FloatConfig::make('default')->extendData())->toBe(['min' => null, 'max' => null, 'step' => 0.1]);
});

it('keeps public text select and callback config extensions available', function () {
	$callback = static fn(): string => 'Callback';

	expect(TextConfig::make('text')->setPlaceholder('0')->setSize('40')->extendData())
		->toBe(['placeholder' => '0', 'size' => '40'])
		->and(SelectConfig::make('choice')->setOptions([0 => 'Zero', 1 => 'One'])->extendData())
		->toBe([2 => [0 => 'Zero', 1 => 'One']])
		->and(CallbackConfig::make('callback')->setCallback($callback)->extendData())->toBe(['callback' => $callback]);
});

it('serializes subclass settings without optional parameter storage', function () {
	$config = new class('custom') extends TextConfig {
		public function __construct(string $name)
		{
			parent::__construct($name);
			unset($this->params);
		}
	};

	expect($config->toArray())->toBe([0 => 'text', 1 => 'custom']);
});

it('accepts absolute URL schemes without path separators as form actions', function () {
	$builder = TableBuilder::make('table', 'Table')->setFormAction('mailto:user@example.com');

	expect($builder->build()['base_href'])->toBe('mailto:user@example.com')
		->and($builder->build()['form']['href'])->toBe('mailto:user@example.com');
});

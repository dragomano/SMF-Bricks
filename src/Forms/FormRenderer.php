<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\RendererInterface;
use InvalidArgumentException;
use Nette\Utils\Html;

use function array_filter;
use function in_array;

use const ARRAY_FILTER_USE_KEY;

class FormRenderer implements RendererInterface
{
	public function render(array $data): void
	{
		$form = Html::el('form', ['accept-charset' => 'utf-8']);

		$this->prepareForm($form, $data);

		echo $form;

		if (! empty($data['script'])) {
			echo Html::el('script')->addHtml($data['script']);
		}
	}

	private function prepareForm(Html $form, array $data): void
	{
		$form
			->id($data['id'])
            ->name($data['name'] ?? $data['id'])
			->action($data['action'] ?? null)
			->method($data['method'] ?? null)
			->enctype($data['enctype'] ?? null)
			->class($data['class'] ?? null);

		$data['title'] && $form->addHtml(
			Html::el('div', ['class' => 'cat_bar'])
				->addHtml(Html::el('h3', ['class' => 'cat' . 'bg'])->setText($data['title']))
		);

		$body = Html::el();

		$this->prepareFields($body, $data);
		$this->prepareButtons($body, $data);

		$form->addHtml(
			Html::el('div', ['class' => $data['body_class'] ?? null])->addHtml($body)
		);
	}

	private function prepareFields(Html $body, array $data): void
	{
		foreach ($data['fields'] as $field) {
			if (! $field instanceof Field) {
				$body->addHtml($this->renderContainer('field', $field['class'], $field['fields']));

				continue;
			}

			$body->addHtml($this->renderField($field->toArray()));
		}
	}

	private function prepareButtons(Html $body, array $data): void
	{
		if (empty($data['buttons']))
			return;

		foreach ($data['buttons'] as $button) {
			if (! $button instanceof Button) {
				$body->addHtml($this->renderContainer('button', $button['class'], $button['buttons']));

				continue;
			}

			$body->addHtml($this->renderButton($button->toArray()));
		}
	}

	private function renderContainer(string $type, string $class, array $elements): Html
	{
		$container = Html::el('div', ['class' => $class]);

		foreach ($elements as $element) {
			if ($type === 'field') {
				$container->addHtml($this->renderField($element->toArray()));
			} elseif ($type === 'button') {
				$container->addHtml($this->renderButton($element->toArray()));
			}
		}

		return $container;
	}

	private function renderField(array $field): Html
	{
		$text = $field['label'];

		unset($field['label']);

		$name = match (true) {
			in_array($field['type'], HtmlFieldType::getInputTypes()) => 'input',
			default => $field['type'],
		};

		$result = Html::el($name, $field)->name($field['name']);

		$this->populateFieldValue($result, $field);

		if ($field['type'] === HtmlFieldType::RADIO->value) {
			$result = $this->prepareRadioOptions($field);
		} elseif ($name === HtmlFieldType::SELECT->value) {
			$result = $this->prepareSelectOptions($field);
		}

		if (in_array($field['type'], [HtmlFieldType::SELECT->value, HtmlFieldType::TEXTAREA->value])) {
			$result->type(null);
		}

		if ($field['type'] === HtmlFieldType::HIDDEN->value) {
			return $result;
		}

		return Html::el('dl', ['class' => 'settings'])
			->addHtml(Html::el('dt')
				->addHtml(Html::el('label', $text)
					->for($field['name'])
					->class('strong')))
			->addHtml(Html::el('dd')->addHtml($result));
	}

	private function renderButton(array $button): Html
	{
		return Html::el('input')
			->id($button['id'] ?? null)
			->name($button['name'])
			->value($button['value'])
			->class($button['class'] ?? null)
			->style($button['style'] ?? null)
			->type($button['type'] ?? null);
	}

	private function populateFieldValue(Html $result, array &$field): void
	{
		if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST')
			return;

		if ($field['type'] === HtmlFieldType::CHECKBOX->value) {
			$result->checked((bool) ($_POST[$field['name']] ?? false));
		}

		if (! isset($_POST[$field['name']]))
			return;

		if ($field['type'] === HtmlFieldType::TEXTAREA->value) {
			$result->setText($_POST[$field['name']]);
		} elseif (in_array($field['type'], [HtmlFieldType::RADIO->value, HtmlFieldType::SELECT->value])) {
			$field['selected'] = $_POST[$field['name']];
		} else {
			$result->value($_POST[$field['name']]);
		}
	}

	private function prepareRadioOptions(array $field): Html
	{
		if (empty($field['options'])) {
			throw new InvalidArgumentException('The radio field options are missing');
		}

		$result = new Html();
		foreach ($field['options'] as $key => $value) {
			$selected = $field['selected'] ?? false;

			$result->addHtml(
				Html::el('input', $value)
					->name($field['name'])
					->value($key)
					->type(HtmlFieldType::RADIO->value)
					->checked($selected === $key)
			);
		}

		return $result;
	}

	private function prepareSelectOptions(array $field): Html
	{
		if (empty($field['options'])) {
			throw new InvalidArgumentException('The select field options are missing');
		}

		$result = Html::el(
			HtmlFieldType::SELECT->value,
			array_filter(
				$field,
				fn($key) => ! in_array($key, ['value', 'selected', 'options', 'type']),
				ARRAY_FILTER_USE_KEY
			)
		);

		$result->name($field['name']);

		foreach ($field['options'] as $key => $value) {
			$selected = $field['selected'] ?? false;

			$result->addHtml(
				Html::el('option', $value)
					->value($key)
					->selected($selected === $key)
			);
		}

		return $result;
	}
}

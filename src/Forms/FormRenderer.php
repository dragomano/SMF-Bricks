<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\ButtonInterface;
use Bugo\Bricks\Forms\Interfaces\FieldInterface;
use Bugo\Bricks\StringRendererInterface;
use InvalidArgumentException;
use Nette\Utils\Html;
use Stringable;

use function array_filter;
use function get_debug_type;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;

use const ARRAY_FILTER_USE_KEY;

class FormRenderer implements StringRendererInterface
{
	private ?RequestData $request = null;

	/**
	 * Replaces the request data that is taken from the superglobals by default.
	 * Useful for tests and for rendering forms outside of a web request context.
	 */
	public function withRequest(RequestData $request): static
	{
		$clone = clone $this;
		$clone->request = $request;

		return $clone;
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): string
	{
		$request = $this->request ?? RequestData::fromGlobals();
		$form    = Html::el('form', ['accept-charset' => 'utf-8']);

		$this->prepareForm($form, $data, $request);

		$result = (string) $form;

		if ((bool) ($data['script'] ?? false)) {
			$result .= (string) Html::el('script')->setHtml($this->htmlText($data['script']));
		}

		return $result;
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	private function prepareForm(Html $form, array $data, RequestData $request): void
	{
		$form->addAttributes([
			'id'      => $data['id'],
			'name'    => $data['name'] ?? $data['id'],
			'action'  => $data['action'] ?? null,
			'method'  => $data['method'] ?? null,
			'enctype' => $data['enctype'] ?? null,
			'class'   => $data['class'] ?? null,
			'style'   => $data['style'] ?? null,
			'target'  => $data['target'] ?? null,
		]);

		if ((bool) $data['title']) {
			$form->addHtml(
				Html::el('div', ['class' => 'cat_bar'])
					->addHtml(Html::el('h3', ['class' => 'cat' . 'bg'])->setText($data['title']))
			);
		}

		$body = Html::el();

		$this->prepareFields($body, $data, $request);
		$this->prepareButtons($body, $data, $request);

		$form->addHtml(
			Html::el('div', ['class' => $data['body_class'] ?? null])->addHtml($body)
		);
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	private function prepareFields(Html $body, array $data, RequestData $request): void
	{
		$fields = $data['fields'] ?? [];

		if (! is_array($fields)) {
			throw new InvalidArgumentException('Form fields must be an array');
		}

		foreach ($fields as $field) {
			if ($field instanceof FieldInterface) {
				$body->addHtml($this->renderField($field->toArray(), $request));

				continue;
			}

			if (! is_array($field) || ! isset($field['fields']) || ! is_array($field['fields'])) {
				throw new InvalidArgumentException(sprintf(
					'Field must be an instance of %s or a container array with the "fields" key, %s given',
					FieldInterface::class,
					get_debug_type($field)
				));
			}

			$body->addHtml($this->renderContainer('field', $this->htmlText($field['class'] ?? ''), $field['fields'], $request));
		}
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	private function prepareButtons(Html $body, array $data, RequestData $request): void
	{
		$buttons = $data['buttons'] ?? [];

		if (! is_array($buttons)) {
			throw new InvalidArgumentException('Form buttons must be an array');
		}

		foreach ($buttons as $button) {
			if ($button instanceof ButtonInterface) {
				$body->addHtml($this->renderButton($button->toArray()));

				continue;
			}

			if (! is_array($button) || ! isset($button['buttons']) || ! is_array($button['buttons'])) {
				throw new InvalidArgumentException(sprintf(
					'Button must be an instance of %s or a container array with the "buttons" key, %s given',
					ButtonInterface::class,
					get_debug_type($button)
				));
			}

			$body->addHtml($this->renderContainer('button', $this->htmlText($button['class'] ?? ''), $button['buttons'], $request));
		}
	}

	/**
	 * @param array<int|string, mixed> $elements
	 */
	private function renderContainer(string $type, string $class, array $elements, RequestData $request): Html
	{
		$container = Html::el('div', ['class' => $class]);

		foreach ($elements as $element) {
			if ($type === 'field' && $element instanceof FieldInterface) {
				$container->addHtml($this->renderField($element->toArray(), $request));
			} elseif ($type === 'button' && $element instanceof ButtonInterface) {
				$container->addHtml($this->renderButton($element->toArray()));
			} else {
				throw new InvalidArgumentException(sprintf(
					'Container element must be an instance of %s, %s given',
					$type === 'field' ? FieldInterface::class : ButtonInterface::class,
					get_debug_type($element)
				));
			}
		}

		return $container;
	}

	/**
	 * @param array<string, mixed> $field
	 */
	private function renderField(array $field, RequestData $request): Html
	{
		if (! isset($field['type'], $field['name']) || ! is_string($field['type']) || ! is_string($field['name'])) {
			throw new InvalidArgumentException('The field data must contain the "type" and "name" keys');
		}

		$text = $this->htmlText($field['label'] ?? '');

		unset($field['label']);

		$name   = HtmlFieldType::from($field['type'])->getTagName();
		$result = Html::el($name, $field)->name($field['name']);

		$this->populateFieldValue($result, $field, $request);

		if ($field['type'] === HtmlFieldType::RADIO->value) {
			$result = $this->prepareRadioOptions($field);
		} elseif ($name === HtmlFieldType::SELECT->value) {
			$result = $this->prepareSelectOptions($field);
		}

		if (in_array($field['type'], [HtmlFieldType::SELECT->value, HtmlFieldType::TEXTAREA->value], true)) {
			$result->type(null);
		}

		if ($field['type'] === HtmlFieldType::HIDDEN->value) {
			return $result;
		}

		// A group of radio buttons has no single control to point at, so the group
		// is described by a legend and every option is wrapped into its own label.
		if ($field['type'] === HtmlFieldType::RADIO->value) {
			return Html::el('fieldset', ['class' => 'settings'])
				->addHtml(Html::el('legend')->class('strong')->setText($text))
				->addHtml($result);
		}

		return Html::el('dl', ['class' => 'settings'])
			->addHtml(Html::el('dt')
				->addHtml(Html::el('label', $text)
					->class('strong')
					->setAttribute('for', $field['id'] ?? $field['name'])))
			->addHtml(Html::el('dd')->addHtml($result));
	}

	/**
	 * @param array<string, mixed> $button
	 */
	private function renderButton(array $button): Html
	{
		if (! isset($button['name'])) {
			throw new InvalidArgumentException('The button data must contain the "name" key');
		}

		return Html::el('input', [
			'id'    => $button['id'] ?? null,
			'name'  => $button['name'],
			'value' => $button['value'] ?? null,
			'class' => $button['class'] ?? null,
			'style' => $button['style'] ?? null,
			'type'  => $button['type'] ?? null,
		]);
	}

	/**
	 * @param array<string, mixed>&array{type: string, name: string} $field
	 */
	private function populateFieldValue(Html $result, array &$field, RequestData $request): void
	{
		if (! $request->isPost())
			return;

		if ($field['type'] === HtmlFieldType::CHECKBOX->value) {
			$result->checked((bool) ($request->post[$field['name']] ?? false));
		}

		if (! isset($request->post[$field['name']]))
			return;

		if ($field['type'] === HtmlFieldType::TEXTAREA->value) {
			$result->setText($request->post[$field['name']]);
		} elseif (in_array($field['type'], [HtmlFieldType::RADIO->value, HtmlFieldType::SELECT->value], true)) {
			$field['selected'] = $request->post[$field['name']];
		} else {
			$result->setAttribute('value', $request->post[$field['name']]);
		}
	}

	/**
	 * @param array<string, mixed> $field
	 */
	private function prepareRadioOptions(array $field): Html
	{
		$options = $field['options'] ?? [];

		if (! is_array($options) || $options === []) {
			throw new InvalidArgumentException('The radio field options are missing');
		}

		$result = new Html();
		foreach ($options as $key => $value) {
			$selected = $field['selected'] ?? false;

			$result->addHtml(
				Html::el('label')
					->addHtml(
						Html::el('input')
							->setAttribute('name', $field['name'])
							->setAttribute('value', $key)
							->type(HtmlFieldType::RADIO->value)
							->checked($selected === $key)
					)
					->addText(' ' . $this->htmlText($value))
			);
		}

		return $result;
	}

	/**
	 * @param array<string, mixed> $field
	 */
	private function prepareSelectOptions(array $field): Html
	{
		$options = $field['options'] ?? [];

		if (! is_array($options) || $options === []) {
			throw new InvalidArgumentException('The select field options are missing');
		}

		$result = Html::el(
			HtmlFieldType::SELECT->value,
			array_filter(
				$field,
				fn($key) => ! in_array($key, ['value', 'selected', 'options', 'type'], true),
				ARRAY_FILTER_USE_KEY
			)
		);

		foreach ($options as $key => $value) {
			$selected = $field['selected'] ?? false;
			$option = Html::el('option');

			if (is_array($value)) {
				foreach ($value as $attribute => $attributeValue) {
					$option->setAttribute((string) $attribute, $attributeValue);
				}
			} else {
				$option->setText($this->htmlText($value));
			}

			$result->addHtml(
				$option
					->setAttribute('value', $key)
					->selected($selected === $key)
			);
		}

		return $result;
	}

	private function htmlText(mixed $value): string
	{
		if ($value === null || is_scalar($value) || $value instanceof Stringable) {
			return (string) $value;
		}

		throw new InvalidArgumentException('HTML text must be a scalar, stringable object or null');
	}
}

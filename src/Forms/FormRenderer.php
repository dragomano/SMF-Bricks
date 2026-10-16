<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\ButtonInterface;
use Bugo\Bricks\Forms\Interfaces\FieldInterface;
use Bugo\Bricks\StringRendererInterface;
use InvalidArgumentException;
use Nette\Utils\Html;

use function array_filter;
use function get_debug_type;
use function in_array;
use function is_array;
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

		$form = Html::el('form', ['accept-charset' => 'utf-8']);

		$this->prepareForm($form, $data, $request);

		$result = (string) $form;

		if (! empty($data['script'])) {
			$result .= (string) Html::el('script')->addHtml($data['script']);
		}

		return $result;
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	private function prepareForm(Html $form, array $data, RequestData $request): void
	{
		$form
			->id($data['id'])
            ->name($data['name'] ?? $data['id'])
			->action($data['action'] ?? null)
			->method($data['method'] ?? null)
			->enctype($data['enctype'] ?? null)
			->class($data['class'] ?? null)
			->style($data['style'] ?? null)
			->target($data['target'] ?? null);

		$data['title'] && $form->addHtml(
			Html::el('div', ['class' => 'cat_bar'])
				->addHtml(Html::el('h3', ['class' => 'cat' . 'bg'])->setText($data['title']))
		);

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
		foreach ($data['fields'] ?? [] as $field) {
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

			$body->addHtml($this->renderContainer('field', $field['class'] ?? '', $field['fields'], $request));
		}
	}

	/**
	 * @param array<int|string, mixed> $data
	 */
	private function prepareButtons(Html $body, array $data, RequestData $request): void
	{
		if (empty($data['buttons']))
			return;

		foreach ($data['buttons'] as $button) {
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

			$body->addHtml($this->renderContainer('button', $button['class'] ?? '', $button['buttons'], $request));
		}
	}

	/**
	 * @param array<int, mixed> $elements
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
		if (! isset($field['type'], $field['name'])) {
			throw new InvalidArgumentException('The field data must contain the "type" and "name" keys');
		}

		$text = (string) ($field['label'] ?? '');

		unset($field['label']);

		$name   = HtmlFieldType::from($field['type'])->getTagName();
		$result = Html::el($name, $field)->name($field['name']);

		$this->populateFieldValue($result, $field, $request);

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
					->for($field['id'] ?? $field['name'])))
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

		return Html::el('input')
			->id($button['id'] ?? null)
			->name($button['name'])
			->value($button['value'] ?? null)
			->class($button['class'] ?? null)
			->style($button['style'] ?? null)
			->type($button['type'] ?? null);
	}

	/**
	 * @param array<string, mixed> $field
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
		} elseif (in_array($field['type'], [HtmlFieldType::RADIO->value, HtmlFieldType::SELECT->value])) {
			$field['selected'] = $request->post[$field['name']];
		} else {
			$result->value($request->post[$field['name']]);
		}
	}

	/**
	 * @param array<string, mixed> $field
	 */
	private function prepareRadioOptions(array $field): Html
	{
		if (empty($field['options'])) {
			throw new InvalidArgumentException('The radio field options are missing');
		}

		$result = new Html();
		foreach ($field['options'] as $key => $value) {
			$selected = $field['selected'] ?? false;

			$result->addHtml(
				Html::el('label')
					->addHtml(
						Html::el('input')
							->name($field['name'])
							->value($key)
							->type(HtmlFieldType::RADIO->value)
							->checked($selected === $key)
					)
					->addText(' ' . $value)
			);
		}

		return $result;
	}

	/**
	 * @param array<string, mixed> $field
	 */
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

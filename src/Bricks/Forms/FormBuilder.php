<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\HasShortMethods;
use InvalidArgumentException;

/**
 * @method self id(string $id)
 * @method self name(string $name)
 * @method self title(string $title)
 * @method self action(string $action)
 * @method self method(string $method = 'post')
 * @method self class(string $class)
 * @method self target(string $target)
 * @method self script(string $script)
 */
class FormBuilder implements FormBuilderInterface
{
	use HasShortMethods;

	private array $attributes = [];

	protected function __construct(string $id, string $title)
	{
		$this->setId($id);
		$this->setTitle($title);
		$this->setMethod();
	}

	public static function make(string $id, string $title): static
	{
		return new static($id, $title);
	}

	public function setId(string $id): self
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	public function setName(string $name): self
	{
		$this->attributes['name'] = $name;

		return $this;
	}

	public function setTitle(string $title): self
	{
		$this->attributes['title'] = $title;

		return $this;
	}

	public function setAction(string $action): self
	{
		$this->attributes['action'] = $action;

		return $this;
	}

	public function setMethod(string $method = 'post'): self
	{
		$this->attributes['method'] = $method;

		return $this;
	}

	public function setClass(string $class): self
	{
		$this->attributes['class'] = $class;

		return $this;
	}

	public function setTarget(string $target): self
	{
		$this->attributes['target'] = $target;

		return $this;
	}

	public function withParams(
		?string $action = null,
		?string $method = null,
		?bool $multipart = null,
		?string $class = null,
		?string $target = null
	): self {
		if ($action) {
			$this->setAction($action);
		}

		if ($method) {
			$this->setMethod($method);
		}

		if ($multipart) {
			$this->attributes['enctype'] = 'multipart/form-data';
		}

		if ($class) {
			$this->setClass($class);
		}

		if ($target) {
			$this->setTarget($target);
		}

		return $this;
	}

	/**
	 * @param array<Field> $fields
	 */
	public function addFields(array $fields, ?string $containerClass = null, ?string $fieldClass = null): self
	{
		$children = [];
		foreach ($fields as $field) {
			if (! $field instanceof Field) {
				throw new InvalidArgumentException(
					sprintf(
						'Field must be instance of %s, %s given',
						Field::class,
						get_debug_type($field)
					)
				);
			}

			$fieldClass && $field->setClass($fieldClass);
			$children[] = $field;
		}

		if ($containerClass !== null) {
			$this->attributes['fields'][] = [
				'type'   => 'container',
				'class'  => $containerClass,
				'fields' => $children,
			];
		} else {
			foreach ($children as $childField) {
				$this->addField($childField);
			}
		}

		return $this;
	}

	public function addField(Field $field): self
	{
		$this->attributes['fields'][$field->getName()] = $field;

		return $this;
	}

	public function removeField(string $name): self
	{
		unset($this->attributes['fields'][$name]);

		return $this;
	}

	/**
	 * @param array<Button> $buttons
	 */
	public function addButtons(array $buttons, ?string $containerClass = null, ?string $buttonClass = null): self
	{
		$children = [];
		foreach ($buttons as $button) {
			if (! $button instanceof Button) {
				throw new InvalidArgumentException(
					sprintf(
						'Button must be instance of %s, %s given',
						Button::class,
						get_debug_type($button)
					)
				);
			}

			$buttonClass && $button->setClass($buttonClass);
			$children[] = $button;
		}

		if ($containerClass !== null) {
			$this->attributes['buttons'][] = [
				'type'    => 'container',
				'class'   => $containerClass,
				'buttons' => $children,
			];
		} else {
			foreach ($children as $childButton) {
				$this->addButton($childButton);
			}
		}

		return $this;
	}

	public function addButton(Button $button): self
	{
		$this->attributes['buttons'][$button->getName()] = $button;

		return $this;
	}

	public function removeButton(string $name): self
	{
		unset($this->attributes['buttons'][$name]);

		return $this;
	}

	public function setScript(string $script): self
	{
		$this->attributes['script'] = $script;

		return $this;
	}

	public function removeScript(): self
	{
		unset($this->attributes['script']);

		return $this;
	}

	public function setBodyClass(string $class): self
	{
		$this->attributes['body_class'] = $class;

		return $this;
	}

	public function addHiddenFields(array $fields): self
	{
		foreach ($fields as $name => $value) {
			$this->addField(HiddenField::make($name)->setValue($value));
		}

		return $this;
	}

	public function getId(): string
	{
		return $this->attributes['id'];
	}

	public function getTitle(): string
	{
		return $this->attributes['title'];
	}

	public function build(): array
	{
		return $this->attributes;
	}
}

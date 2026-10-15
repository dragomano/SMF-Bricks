<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use Bugo\Bricks\Forms\Interfaces\ButtonInterface;
use Bugo\Bricks\Forms\Interfaces\FieldInterface;
use Bugo\Bricks\Forms\Interfaces\FormBuilderInterface;
use Bugo\Bricks\HasShortMethods;
use InvalidArgumentException;

use function array_filter;
use function array_values;
use function is_array;

/**
 * @method self id(string $id)
 * @method self name(string $name)
 * @method self title(string $title)
 * @method self action(string $action)
 * @method self method(string $method = 'post')
 * @method self class(string $class)
 * @method self target(string $target)
 * @method self script(string $script)
 *
 * @phpstan-consistent-constructor
 */
class FormBuilder implements FormBuilderInterface
{
	use HasShortMethods;

	/** @var array<string, mixed> */
	private array $attributes = [];

	protected function __construct(string $id, string $title = '')
	{
		$this->setId($id);
		$this->setTitle($title);
		$this->setMethod();
	}

	public static function make(string $id, string $title = ''): static
	{
		return new static($id, $title);
	}

	public function setId(string $id): static
	{
		$this->attributes['id'] = $id;

		return $this;
	}

	public function setName(string $name): static
	{
		$this->attributes['name'] = $name;

		return $this;
	}

	public function setTitle(string $title): static
	{
		$this->attributes['title'] = $title;

		return $this;
	}

	public function setAction(string $action): static
	{
		$this->attributes['action'] = $action;

		return $this;
	}

	public function setMethod(string $method = 'post'): static
	{
		$this->attributes['method'] = $method;

		return $this;
	}

	public function setClass(string $class): static
	{
		$this->attributes['class'] = $class;

		return $this;
	}

	public function setStyle(string $style): static
	{
		$this->attributes['style'] = $style;

		return $this;
	}

	public function setTarget(string $target): static
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
	): static {
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
	 * @param array<FieldInterface> $fields
	 */
	public function addFields(array $fields, ?string $containerClass = null, ?string $fieldClass = null): static
	{
		$children = [];
		foreach ($fields as $field) {
			if (! $field instanceof FieldInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Field must be instance of %s, %s given',
						FieldInterface::class,
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

	public function addField(FieldInterface $field): static
	{
		$this->attributes['fields'][$field->getName()] = $field;

		return $this;
	}

	public function removeField(string $name): static
	{
		unset($this->attributes['fields'][$name]);

		foreach ($this->attributes['fields'] ?? [] as $key => $container) {
			if (! is_array($container) || empty($container['fields']) || ! is_array($container['fields'])) {
				continue;
			}

			$this->attributes['fields'][$key]['fields'] = array_values(array_filter(
				$container['fields'],
				static fn($field) => ! $field instanceof FieldInterface || $field->getName() !== $name
			));
		}

		return $this;
	}

	/**
	 * @param array<ButtonInterface> $buttons
	 */
	public function addButtons(array $buttons, ?string $containerClass = null, ?string $buttonClass = null): static
	{
		$children = [];
		foreach ($buttons as $button) {
			if (! $button instanceof ButtonInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Button must be instance of %s, %s given',
						ButtonInterface::class,
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

	public function addButton(ButtonInterface $button): static
	{
		$this->attributes['buttons'][$button->getName()] = $button;

		return $this;
	}

	public function removeButton(string $name): static
	{
		unset($this->attributes['buttons'][$name]);

		foreach ($this->attributes['buttons'] ?? [] as $key => $container) {
			if (! is_array($container) || empty($container['buttons']) || ! is_array($container['buttons'])) {
				continue;
			}

			$this->attributes['buttons'][$key]['buttons'] = array_values(array_filter(
				$container['buttons'],
				static fn($button) => ! $button instanceof ButtonInterface || $button->getName() !== $name
			));
		}

		return $this;
	}

	public function setScript(string $script): static
	{
		$this->attributes['script'] = $script;

		return $this;
	}

	public function removeScript(): static
	{
		unset($this->attributes['script']);

		return $this;
	}

	public function setBodyClass(string $class): static
	{
		$this->attributes['body_class'] = $class;

		return $this;
	}

	/**
	 * @param array<string, mixed> $fields
	 */
	public function addHiddenFields(array $fields): static
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

	/**
	 * @return array<string, mixed>
	 */
	public function build(): array
	{
		return $this->attributes;
	}
}

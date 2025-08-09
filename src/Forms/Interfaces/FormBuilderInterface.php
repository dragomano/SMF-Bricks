<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms\Interfaces;

use Bugo\Bricks\BuilderInterface;
use Bugo\Bricks\Forms\Button;
use Bugo\Bricks\Forms\Field;

interface FormBuilderInterface extends BuilderInterface
{
	public function setClass(string $class): self;

	public function withParams(
		?string $action = null,
		?string $method = null,
		?bool $multipart = null,
		?string $class = null
	): self;

	public function addFields(array $fields, ?string $containerClass = null, ?string $fieldClass = null): self;

	public function addField(Field $field): self;

	public function removeField(string $name): self;

	public function addButtons(array $buttons, ?string $containerClass = null, ?string $buttonClass = null): self;

	public function addButton(Button $button): self;

	public function removeButton(string $name): self;

	public function setScript(string $script): self;

	public function removeScript(): self;

	public function setBodyClass(string $class): self;

	public function addHiddenFields(array $fields): self;
}

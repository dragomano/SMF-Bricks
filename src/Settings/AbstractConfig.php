<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

use Bugo\Bricks\Settings\Interfaces\ConfigInterface;

use function array_filter;
use function array_merge;
use function get_object_vars;
use function is_array;
use function ksort;

abstract class AbstractConfig implements ConfigInterface
{
	protected string $type;

	protected string $tab;

	protected array $params = [];

	protected function __construct(protected string $name) {}

	public static function make(string $name): static
	{
		return new static($name);
	}

	public function setAttributes(array $attributes): self
	{
		foreach ($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}

		return $this;
	}

	public function setAttribute(string $name, string $value): self
	{
		$this->params['attributes'][$name] = $value;

		return $this;
	}

	public function setLabel(string $label): self
	{
		$this->params['label'] = $label;

		return $this;
	}

	public function setHelp(string $help): self
	{
		$this->params['help'] = $help;

		return $this;
	}

	public function setPreInput(string $input): self
	{
		$this->params['preinput'] = $input;

		return $this;
	}

	public function setPostInput(string $input): self
	{
		$this->params['postinput'] = $input;

		return $this;
	}

	public function setSubText(string $subtext): self
	{
		$this->params['subtext'] = $subtext;

		return $this;
	}

	public function setJavaScript(string $javascript): self
	{
		$this->params['javascript'] = $javascript;

		return $this;
	}

	public function setOnChange(string $onchange): self
	{
		$this->params['onchange'] = $onchange;

		return $this;
	}

	public function setDisabled(bool $disabled): self
	{
		$this->params['disabled'] = $disabled;

		return $this;
	}

	public function setInvalid(bool $invalid): self
	{
		$this->params['invalid'] = $invalid;

		return $this;
	}

	public function setTab(string $tab): self
	{
		$this->tab = $tab;

		return $this;
	}

	/**
	 * Additional config var data (title, options, min/max/step, callback, etc.).
	 */
	protected function extendData(): array
	{
		return [];
	}

	public function toArray(): array
	{
		$data = get_object_vars($this);
		$data[0] = $data['type'] ?? null;
		$data[1] = $data['name'] ?? null;

		unset($data['type'], $data['name']);

		$data = array_merge($data, $this->extendData());

		if (isset($data['params']) && is_array($data['params'])) {
			$data = array_merge($data, $data['params']);

			unset($data['params']);
		}

		ksort($data);

		// Only null and '' are dropped: zeroes and false are valid setting values,
		// while an empty string must stay dropped so that DividerConfig keeps turning
		// into '' in ConfigBuilder::build()
		return array_filter($data, static fn($value) => $value !== null && $value !== '');
	}
}

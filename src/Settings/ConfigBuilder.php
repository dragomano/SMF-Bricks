<?php declare(strict_types=1);

namespace Bugo\Bricks\Settings;

use Bugo\Bricks\Settings\Interfaces\ConfigBuilderInterface;
use Bugo\Bricks\Settings\Interfaces\ConfigInterface;
use InvalidArgumentException;

use function array_map;
use function get_debug_type;
use function sprintf;

class ConfigBuilder implements ConfigBuilderInterface
{
	private array $vars = [];

	protected function __construct() {}

	public static function make(): static
	{
		return new static();
	}

	/**
	 * @param array<ConfigInterface> $vars
	 */
	public function addVars(array $vars): self
	{
		foreach ($vars as $var) {
			if (! $var instanceof ConfigInterface) {
				throw new InvalidArgumentException(
					sprintf(
						'Var must be instance of %s, %s given',
						ConfigInterface::class,
						get_debug_type($var)
					)
				);
			}

			$this->addVar($var);
		}

		return $this;
	}

	public function addVar(ConfigInterface $var): self
	{
		$this->vars[] = $var;

		return $this;
	}

	public function build(): array
	{
		return array_map(fn(ConfigInterface $var) => $var->toArray() ?: '', $this->vars);
	}
}

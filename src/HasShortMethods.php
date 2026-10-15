<?php declare(strict_types=1);

namespace Bugo\Bricks;

use BadMethodCallException;

use function method_exists;
use function ucfirst;

trait HasShortMethods
{
	/**
	 * @param array<int, mixed> $arguments
	 */
	public function __call(string $name, array $arguments): mixed
	{
		$methodName = 'set' . ucfirst($name);

		if (! method_exists($this, $methodName)) {
			throw new BadMethodCallException("Method $name does not exist");
		}

		return $this->{$methodName}(...$arguments);
	}
}

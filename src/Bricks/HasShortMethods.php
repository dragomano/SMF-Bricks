<?php declare(strict_types=1);

namespace Bugo\Bricks;

use BadMethodCallException;

use function call_user_func_array;
use function method_exists;
use function ucfirst;

trait HasShortMethods
{
	public function __call(string $name, array $arguments)
	{
		$methodName = 'set' . ucfirst($name);

		if (method_exists($this, $methodName)) {
			return call_user_func_array([$this, $methodName], $arguments);
		}

		throw new BadMethodCallException("Method $name does not exist");
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

use function is_string;

/**
 * Minimal snapshot of the request data used to fill in form field values.
 */
final readonly class RequestData
{
	/**
	 * @param array<string, mixed> $post
	 */
	public function __construct(
		public string $method = 'GET',
		public array $post = [],
	) {}

	public static function fromGlobals(): self
	{
		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

		return new self(is_string($method) ? $method : 'GET', $_POST);
	}

	public function isPost(): bool
	{
		return $this->method === 'POST';
	}
}

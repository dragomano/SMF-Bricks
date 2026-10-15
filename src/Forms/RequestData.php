<?php declare(strict_types=1);

namespace Bugo\Bricks\Forms;

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
		return new self($_SERVER['REQUEST_METHOD'] ?? 'GET', $_POST);
	}

	public function isPost(): bool
	{
		return $this->method === 'POST';
	}
}

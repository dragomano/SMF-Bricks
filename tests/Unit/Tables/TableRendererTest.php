<?php declare(strict_types=1);

use Bugo\Bricks\Tables\TableRenderer;

/**
 * Renders the table and turns every PHP warning into an exception,
 * so that missing array keys are not silently ignored.
 */
function renderTableQuietly(callable $callback): string
{
	set_error_handler(static function (int $severity, string $message): bool {
		throw new ErrorException($message);
	});

	try {
		ob_start();

		$callback();

		return (string) ob_get_clean();
	} finally {
		restore_error_handler();
	}
}

it('renders a cell without a value key', function () {
	$data = [
		'id'   => 'table_id',
		'rows' => [
			['data' => [['class' => 'some_class']]],
		],
	];

	$output = renderTableQuietly(static fn() => (new TableRenderer())->render($data));

	expect($output)->toContain('<td class="some_class"></td>');
});

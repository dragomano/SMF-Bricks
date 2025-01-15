<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

use function array_map;
use function count;
use function header;
use function is_array;
use function json_encode;
use function ob_get_clean;
use function ob_start;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class ApiTablePresenter extends AbstractTablePresenter
{
	public static function show(TableBuilderInterface $builder): void
	{
		header('Content-Type: application/json; charset=UTF-8');

		ob_start();

		TablePresenter::show($builder->removeScript());

		ob_get_clean();

		$data = Utils::$context[Utils::$context['default_list']];

		$itemsPerPage = $data['items_per_page'];

		$outputData = [
			'data' => [],
			'meta' => [
				'total' => count($data['rows'] ?? []),
				'per_page' => $itemsPerPage,
				'current_page' => (int) ($_GET['start'] ?? 0) / $itemsPerPage + 1,
				'columns' => $data['headers'] ?? []
			]
		];

		foreach ($data['rows'] as $row) {
			$formattedRow = array_map(
				fn($value) => is_array($value) && isset($value['value']) ? $value : ['value' => $value],
				$row['data']
			);
			$outputData['data'][] = $formattedRow;
		}

		echo json_encode($outputData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}

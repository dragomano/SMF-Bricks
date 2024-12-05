<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

use function array_map;
use function chr;
use function fclose;
use function fopen;
use function fprintf;
use function fputcsv;
use function header;
use function ob_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;
use function strip_tags;
use function trim;

class CsvTablePresenter extends AbstractTablePresenter
{
	public static function show(TableBuilderInterface $builder): void
	{
		ob_get_level() && ob_clean();

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="export.csv"');

		ob_start();
		TablePresenter::show($builder->removeScript());
		ob_get_clean();

		$data = Utils::$context[Utils::$context['default_list']];

		$output = fopen('php://output', 'w');

		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		$headers = array_map(
			static fn($column) => strip_tags((string) ($column['label'] ?? $column['id'] ?? '')),
			$data['headers']
		);

		fputcsv($output, $headers);

		foreach ($data['rows'] ?? [] as $item) {
			$row = [];
			foreach ($data['headers'] as $column) {
				$row[] = strip_tags(trim((string) $item['data'][$column['id']]['value'] ?? ''));
			}

			fputcsv($output, $row);
		}

		fclose($output);
	}
}

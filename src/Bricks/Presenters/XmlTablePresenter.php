<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;
use Spatie\ArrayToXml\ArrayToXml;

use function array_map;
use function header;
use function ob_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;

class XmlTablePresenter extends AbstractTablePresenter
{
	public static function show(TableBuilderInterface $builder): void
	{
		ob_get_level() && ob_clean();

		header('Content-Type: application/xml; charset=UTF-8');

		ob_start();
		TablePresenter::show($builder->removeScript());
		ob_get_clean();

		$data = Utils::$context[Utils::$context['default_list']];

		$xmlArray = [
			'id' => $data['id'],
			'title' => $data['title'],
			'items_per_page' => $data['items_per_page'],
			'headers' => [
				'header' => array_map(fn($header) => [
					'id' => $header['id'],
					'label' => $header['label'],
				], $data['headers']),
			],
			'rows' => [
				'row' => array_map(fn($row) => $row['data'], $data['rows']),
			],
		];

		echo ArrayToXml::convert($xmlArray, 'table', true, 'UTF-8');
	}
}

<?php declare(strict_types=1);

namespace Bugo\Bricks\Presenters;

use Bugo\Bricks\Tables\Interfaces\TableBuilderInterface;
use Bugo\Compat\Utils;

use function header;
use function json_encode;
use function ob_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class JsonTablePresenter extends AbstractTablePresenter
{
	public static function show(TableBuilderInterface $builder): void
	{
		ob_get_level() && ob_clean();

		header('Content-Type: application/json; charset=UTF-8');
		header('Cache-Control: no-cache');

		ob_start();

		TablePresenter::show($builder->removeScript());

		ob_get_clean();

		$data = Utils::$context[Utils::$context['default_list']];

		echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
}

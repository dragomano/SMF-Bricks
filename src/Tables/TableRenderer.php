<?php declare(strict_types=1);

namespace Bugo\Bricks\Tables;

use Bugo\Bricks\StringRendererInterface;
use InvalidArgumentException;
use Nette\Utils\Html;
use Stringable;

use function is_array;
use function is_scalar;

class TableRenderer implements StringRendererInterface
{
	/**
	 * When enabled, the values taken from the data array are escaped instead of being
	 * inserted as raw HTML. Disabled by default to keep the SMF markup working.
	 */
	public function __construct(private readonly bool $escapeData = false) {}

	/**
	 * @param array<int|string, mixed> $data
	 */
	public function render(array $data): string
	{
		$root = Html::el();
		$form = Html::el('form');

		if (isset($data['title'])) {
			$div = Html::el('div')->class('cat_bar');
			$h3  = Html::el('h3')->class('cat' . 'bg');

			$form->addHtml($div->addHtml($this->fill($h3, $data['title'])));
		}

		$table = Html::el('table', ['id' => $data['id']]);
		$tr    = Html::el('tr');

		$headers = $data['headers'] ?? [];
		$rows    = $data['rows'] ?? [];

		if (! is_array($headers) || ! is_array($rows)) {
			throw new InvalidArgumentException('Table headers and rows must be arrays');
		}

		foreach ($headers as $header) {
			if (! is_array($header)) {
				throw new InvalidArgumentException('Table headers must contain arrays');
			}

			$th = Html::el('th', [
				'id'    => $header['id'],
				'class' => $header['class'] ?? null,
				'style' => $header['style'] ?? null,
			]);

			$tr->addHtml($this->fill($th, $header['label']));
		}

		$table->addHtml($tr);

		foreach ($rows as $row) {
			if (! is_array($row) || ! isset($row['data']) || ! is_array($row['data'])) {
				throw new InvalidArgumentException('Table rows must contain a data array');
			}

			$rowData = $row['data'];

			$tr = Html::el('tr');
			$tr->class('window' . 'bg');

			foreach ($rowData as $cell) {
				if (! is_array($cell)) {
					throw new InvalidArgumentException('Table cells must be arrays');
				}

				$td = Html::el('td', [
					'class' => $cell['class'] ?? null,
					'style' => $cell['style'] ?? null,
				]);

				$tr->addHtml($this->fill($td, $cell['value'] ?? ''));
			}

			$table->addHtml($tr);
		}

		$form->addHtml($table);
		$root->addHtml($form);

		if ((bool) ($data['javascript'] ?? false)) {
			$root->addHtml(Html::el('script')->setText($data['javascript']));
		}

		return (string) $root;
	}

	private function fill(Html $element, mixed $value): Html
	{
		$text = is_scalar($value) || $value instanceof Stringable ? (string) $value : '';

		return $this->escapeData ? $element->setText($text) : $element->setHtml($text);
	}
}

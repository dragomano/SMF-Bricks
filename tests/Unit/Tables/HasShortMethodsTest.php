<?php declare(strict_types=1);

use Bugo\Bricks\Tables\Row;
use Bugo\Bricks\HasShortMethods;

beforeEach(function () {
	$this->row = Row::make('some row');
});

it('can provide short variants of known methods', function () {
	expect($this->row->class('btn btn-button')->toArray()['class'])->toBe('btn btn-button')
		->and($this->row->style('height: 100px')->toArray()['style'])->toBe('height: 100px');
});

it('cannot provide short variants of unknown methods', function () {
	$receiver = new class {
		use HasShortMethods {
			__call as private dispatch;
		}

		public int $calls = 0;

		public function __call(string $name, array $arguments): mixed
		{
			if (++$this->calls > 1) {
				throw new LogicException('Unknown aliases must not trigger recursive dispatch');
			}

			return $this->dispatch($name, $arguments);
		}
	};

	expect(fn() => $receiver->blablabla('test'))->toThrow(BadMethodCallException::class, 'Method blablabla does not exist')
		->and($receiver->calls)->toBe(1);
});

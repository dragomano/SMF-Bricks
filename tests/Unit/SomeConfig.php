<?php declare(strict_types=1);

namespace Tests\Unit;

use Bugo\Bricks\Settings\AbstractConfig;

class SomeConfig extends AbstractConfig {
	protected string $type = 'some_type';
};

<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');
use Bugo\Compat\Config;
use Bugo\Compat\Utils;

uses()->beforeAll(function () {
	Config::$sourcedir = __DIR__ . DIRECTORY_SEPARATOR . 'files';

	Utils::$context['default_list'] = 'table_id';
	Utils::$context['table_id'] = [
		'id' => 'table_id',
		'title' => 'Test Table',
		'items_per_page' => 10,
		'headers' => [
			['id' => 'username', 'label' => 'Username'],
			['id' => 'email', 'label' => 'Email'],
		],
		'rows' => [
			['data' => ['username' => ['value' => 'test_user'], 'email' => ['value' => 'test@example.com']]],
			['data' => ['username' => ['value' => 'another_user'], 'email' => ['value' => 'another@example.com']]],
		],
	];
})->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeSuccess', function () {
	return $this->toBe('success');
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

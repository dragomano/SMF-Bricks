<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (in_array('--version', $_SERVER['argv'], true)) {
	echo 'PHPUnit ' . PHPUnit\Runner\Version::id() . PHP_EOL;
	exit(0);
}

register_shutdown_function(static function (): void {
	$result = PHPUnit\TestRunner\TestResult\Facade::result();
	$passed = $result->numberOfTestsRun() > 0
		&& Pest\Result::ok(PHPUnit\TextUI\Configuration\Registry::get(), $result);

	fwrite(STDOUT, $passed
		? sprintf("OK (%d tests, %d assertions)\n", $result->numberOfTestsRun(), $result->numberOfAssertions())
		: "FAILURES!\n");

	if (! $passed) {
		foreach ([...$result->testFailedEvents(), ...$result->testErroredEvents()] as $event) {
			fwrite(STDOUT, $event->asString() . PHP_EOL . $event->throwable()->stackTrace() . PHP_EOL);
		}
	}
});

$junit = null;
foreach ($_SERVER['argv'] as $index => $argument) {
	if (str_starts_with($argument, '--log-junit=')) {
		$junit = substr($argument, strlen('--log-junit='));
	} elseif ($argument === '--log-junit') {
		$junit = $_SERVER['argv'][$index + 1];
	}
}

if ($junit !== null) {
	register_shutdown_function(static function () use ($junit): void {
		if (! is_file($junit)) {
			return;
		}

		$report = new DOMDocument();
		$report->load($junit);

		foreach ($report->getElementsByTagName('testsuite') as $suite) {
			$class = 'P\\' . $suite->getAttribute('name');

			if (class_exists($class, false)) {
				$suite->setAttribute('name', $class);
				$suite->setAttribute('file', realpath($suite->getAttribute('file')));
			}
		}

		$report->save($junit);
	});
}

require dirname(__DIR__) . '/vendor/pestphp/pest/bin/pest';

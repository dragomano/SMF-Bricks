<?php declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
	->withPaths([
		__DIR__ . '/src',
	])
	->withParallel(360)
	->withIndent(indentChar: "\t")
	->withImportNames(importShortClasses: false, removeUnusedImports: true)
	->withPreparedSets(deadCode: true)
	->withPhpSets();

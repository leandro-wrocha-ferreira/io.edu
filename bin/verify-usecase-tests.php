<?php

/**
 * Deterministic Use Case Test Verifier.
 *
 * Ensures that 100% of application Use Cases have a corresponding 1:1 unit test
 * under tests/unit/usecases/ following the DDD-lite architecture guidelines.
 *
 * Usage:
 *   php bin/verify-usecase-tests.php
 *
 * Exit Codes:
 *   0 - All Use Cases have corresponding unit tests.
 *   1 - One or more Use Cases are missing unit tests.
 */

declare(strict_types=1);

$project_root = dirname(__DIR__);
$usecases_dir = $project_root . '/application/usecases';
$tests_dir = $project_root . '/tests/unit/usecases';

if (!is_dir($usecases_dir)) {
	fwrite(STDERR, "Error: Use cases directory not found at: {$usecases_dir}\n");
	exit(1);
}

$directory_iterator = new RecursiveDirectoryIterator($usecases_dir, RecursiveDirectoryIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory_iterator);

$missing_tests = [];
$total_use_cases = 0;
$passed_use_cases = 0;

echo "====================================================\n";
echo " Deterministic Use Case Test Verifier\n";
echo "====================================================\n\n";

/** @var SplFileInfo $file_info */
foreach ($iterator as $file_info) {
	if ($file_info->isDir() || !str_ends_with($file_info->getFilename(), 'UseCase.php')) {
		continue;
	}

	$total_use_cases++;
	$real_path = $file_info->getRealPath();
	$relative_path = substr($real_path, strlen($usecases_dir) + 1);

	// Example: course/CreateCourseUseCase.php -> course/CreateCourseUseCaseTest.php
	$test_relative_path = substr($relative_path, 0, -4) . 'Test.php';
	$expected_test_path = $tests_dir . '/' . $test_relative_path;

	if (file_exists($expected_test_path)) {
		$passed_use_cases++;
		echo sprintf(" [OK]      %-45s -> %s\n", $relative_path, $test_relative_path);
	} else {
		$missing_tests[] = [
			'usecase' => $relative_path,
			'expected_test' => $test_relative_path,
			'expected_full_path' => $expected_test_path,
		];
		echo sprintf(" [MISSING] %-45s -> %s\n", $relative_path, $test_relative_path);
	}
}

echo "\n----------------------------------------------------\n";
echo sprintf("Summary: %d / %d Use Cases have corresponding tests.\n", $passed_use_cases, $total_use_cases);
echo "----------------------------------------------------\n";

if (!empty($missing_tests)) {
	echo "\nFAILED: The following Use Cases are missing unit tests:\n";
	foreach ($missing_tests as $missing) {
		echo "  - {$missing['usecase']} (Expected: tests/unit/usecases/{$missing['expected_test']})\n";
	}
	echo "\nPlease create the missing test files before proceeding.\n\n";
	exit(1);
}

echo "\nSUCCESS: All Use Cases have matching 1:1 unit tests!\n\n";
exit(0);

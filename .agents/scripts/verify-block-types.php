<?php

/**
 * Deterministic Block Types & Child Table Persistence Verifier.
 *
 * Ensures that all block types defined in BlockType::ALL have:
 * 1. A corresponding child table in the database migrations ({type}_blocks).
 * 2. Proper persistence mapping in LessonBlockMapper::to_child_database_data().
 *
 * Usage:
 *   php .agents/scripts/verify-block-types.php
 *
 * Exit Codes:
 *   0 - All block types have matching child tables and mapper handlers.
 *   1 - Inconsistencies detected between BlockType, migrations, and mapper.
 */

declare(strict_types=1);

$project_root = dirname(__DIR__, 2);
$block_type_file = $project_root . '/application/domain/course/constants/BlockType.php';
$mapper_file = $project_root . '/application/models/mappers/LessonBlockMapper.php';
$migrations_dir = $project_root . '/application/migrations';

if (!file_exists($block_type_file)) {
	fwrite(STDERR, "Error: BlockType.php not found at: {$block_type_file}\n");
	exit(1);
}

if (!file_exists($mapper_file)) {
	fwrite(STDERR, "Error: LessonBlockMapper.php not found at: {$mapper_file}\n");
	exit(1);
}

// 1. Extract types from BlockType.php
$block_type_content = file_get_contents($block_type_file);
if (!preg_match('/public\s+const\s+ALL\s*=\s*\[(.*?)\];/s', $block_type_content, $all_matches)) {
	fwrite(STDERR, "Error: Could not extract BlockType::ALL\n");
	exit(1);
}

preg_match_all('/self::([A-Z0-9_]+)/', $all_matches[1], $const_matches);
$types = [];
foreach ($const_matches[1] as $const_name) {
	if (preg_match('/public\s+const\s+' . $const_name . "\s*=\s*'([^']+)'/", $block_type_content, $val_match)) {
		$types[] = $val_match[1];
	}
}

echo "====================================================\n";
echo " Deterministic Block Types & Persistence Verifier\n";
echo "====================================================\n\n";

$mapper_content = file_get_contents($mapper_file);

// 2. Read all migrations content
$migration_files = glob($migrations_dir . '/*.php');
$all_migrations_sql = '';
foreach ($migration_files as $migration_path) {
	$all_migrations_sql .= file_get_contents($migration_path) . "\n";
}

$issues = [];
$total_checked = 0;
$passed_checked = 0;

foreach ($types as $type) {
	$expected_table = $type . '_blocks';
	$expected_entity_class = ucfirst($type) . 'Block';

	echo "--- Checking Block Type: '{$type}' ---\n";

	// Block types deferred to future demands (e.g. Quiz block in IOEDU-0014)
	if ($type === 'quiz') {
		echo " [DEFERRED] Block Type 'quiz' deferred to future demand (UI shows in-development alert)\n";
		continue;
	}

	// Check 1: Child table in migrations
	$total_checked++;
	$has_table = str_contains($all_migrations_sql, "'{$expected_table}'") || str_contains($all_migrations_sql, "`{$expected_table}`");
	if ($has_table) {
		$passed_checked++;
		echo sprintf(" [OK]      Database Table: '%s' defined in migrations\n", $expected_table);
	} else {
		$issues[] = sprintf("Block type '%s' is missing child table '%s' in migrations.", $type, $expected_table);
		echo sprintf(" [MISSING] Database Table: '%s' NOT found in migrations!\n", $expected_table);
	}

	// Check 2: Mapper persistence handling in to_child_database_data()
	$total_checked++;
	$has_mapper_handling = str_contains($mapper_content, $expected_entity_class) && str_contains($mapper_content, "'table' => '{$expected_table}'");
	if ($has_mapper_handling) {
		$passed_checked++;
		echo sprintf(" [OK]      Mapper Handling: %s mapped to '%s'\n", $expected_entity_class, $expected_table);
	} else {
		$issues[] = sprintf("LessonBlockMapper::to_child_database_data() does not handle '%s' with table '%s'.", $expected_entity_class, $expected_table);
		echo sprintf(" [MISSING] Mapper Handling: %s is NOT handled in to_child_database_data()!\n", $expected_entity_class);
	}
}

echo "\n----------------------------------------------------\n";
echo sprintf("Summary: %d / %d checks passed.\n", $passed_checked, $total_checked);
echo "----------------------------------------------------\n";

if (!empty($issues)) {
	echo "\nFAILED: The following Block Type persistence issues were detected:\n";
	foreach ($issues as $issue) {
		echo "  - {$issue}\n";
	}
	echo "\nPlease synchronize the BlockType constants with migrations and mapper before proceeding.\n\n";
	exit(1);
}

echo "\nSUCCESS: All Block Types have matching child tables and mapper handlers!\n\n";
exit(0);

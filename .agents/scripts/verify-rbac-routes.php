<?php

/**
 * Deterministic RBAC Route & Controller Coverage Verifier.
 *
 * Validates that all admin routes and admin controllers have corresponding
 * permission mapping entries in Middleware::$route_permission_map.
 *
 * Usage:
 *   php .agents/scripts/verify-rbac-routes.php
 *
 * Exit Codes:
 *   0 - All admin routes and controllers are protected by RBAC.
 *   1 - One or more admin routes or controllers are missing permissions in Middleware.
 */

declare(strict_types=1);

$project_root = dirname(__DIR__, 2);
$routes_file = $project_root . '/application/config/routes.php';
$middleware_file = $project_root . '/application/hooks/Middleware.php';
$admin_controllers_dir = $project_root . '/application/controllers/admin';

if (!file_exists($routes_file)) {
	fwrite(STDERR, "Error: routes.php not found at: {$routes_file}\n");
	exit(1);
}

if (!file_exists($middleware_file)) {
	fwrite(STDERR, "Error: Middleware.php not found at: {$middleware_file}\n");
	exit(1);
}

// 1. Extract permissions map from Middleware.php
$middleware_content = file_get_contents($middleware_file);
if (!preg_match('/private\s+array\s+\$route_permission_map\s*=\s*\[(.*?)\];/s', $middleware_content, $map_matches)) {
	fwrite(STDERR, "Error: Could not extract \$route_permission_map from Middleware.php\n");
	exit(1);
}

$raw_map_content = $map_matches[1];
preg_match_all("/'([^']+)'\s*=>\s*([^,\n]+)/", $raw_map_content, $entry_matches, PREG_SET_ORDER);

$mapped_routes = [];
foreach ($entry_matches as $match) {
	$mapped_routes[trim($match[1], '/')] = trim($match[2]);
}

echo "====================================================\n";
echo " Deterministic RBAC Route & Middleware Verifier\n";
echo "====================================================\n\n";

$missing_routes = [];
$total_checked = 0;
$passed_checked = 0;

// Helper to check if a route is covered by exact match or prefix
function is_route_covered(string $route, array $map): bool
{
	$clean_route = trim($route, '/');
	if (isset($map[$clean_route])) {
		return true;
	}

	$parts = explode('/', $clean_route);
	// Check prefix (e.g., admin/cursos for admin/cursos/blocos/salvar)
	while (count($parts) > 1) {
		array_pop($parts);
		$prefix = implode('/', $parts);
		if (isset($map[$prefix])) {
			return true;
		}
	}

	return false;
}

// 2. Check admin controllers
echo "--- Verifying Admin Controllers ---\n";
if (is_dir($admin_controllers_dir)) {
	$controller_files = glob($admin_controllers_dir . '/*.php');
	foreach ($controller_files as $controller_path) {
		$controller_name = basename($controller_path, '.php');
		if ($controller_name === 'Dashboard') {
			continue;
		}

		$total_checked++;
		$route_key = 'admin/' . strtolower($controller_name);

		if (is_route_covered($route_key, $mapped_routes)) {
			$passed_checked++;
			echo sprintf(" [OK]      Controller: %-30s (Covered)\n", $controller_name);
		} else {
			$missing_routes[] = [
				'type' => 'controller',
				'target' => $controller_name,
				'suggested_route' => $route_key,
			];
			echo sprintf(" [MISSING] Controller: %-30s (Missing '%s' in Middleware)\n", $controller_name, $route_key);
		}
	}
}

// 3. Check admin routes declared in routes.php
echo "\n--- Verifying Admin Routes in routes.php ---\n";
$routes_content = file_get_contents($routes_file);
preg_match_all('/\$route\[\'(admin\/[^\']+)\'\]\s*=/i', $routes_content, $route_matches);

$unique_admin_routes = array_unique($route_matches[1] ?? []);
foreach ($unique_admin_routes as $raw_route) {
	// Normalize route pattern (remove (:num), (:any), etc.)
	$normalized_route = preg_replace('/\/\(:[a-zA-Z0-9_-]+\)/', '', $raw_route);
	$total_checked++;

	if (is_route_covered($normalized_route, $mapped_routes)) {
		$passed_checked++;
		echo sprintf(" [OK]      Route: %-40s (Covered)\n", $raw_route);
	} else {
		$missing_routes[] = [
			'type' => 'route',
			'target' => $raw_route,
			'suggested_route' => $normalized_route,
		];
		echo sprintf(" [MISSING] Route: %-40s (Not covered in Middleware)\n", $raw_route);
	}
}

echo "\n----------------------------------------------------\n";
echo sprintf("Summary: %d / %d checks passed.\n", $passed_checked, $total_checked);
echo "----------------------------------------------------\n";

if (!empty($missing_routes)) {
	echo "\nFAILED: The following Admin items are missing RBAC mapping in Middleware.php:\n";
	foreach ($missing_routes as $missing) {
		echo sprintf("  - [%s] %s -> Add '%s' => PermissionSlug::... in Middleware.php\n", $missing['type'], $missing['target'], $missing['suggested_route']);
	}
	echo "\nPlease add the required permission mappings before proceeding.\n\n";
	exit(1);
}

echo "\nSUCCESS: All Admin routes and controllers are mapped in Middleware.php!\n\n";
exit(0);

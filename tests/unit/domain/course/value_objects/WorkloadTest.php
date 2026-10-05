<?php

namespace tests\unit\domain\course\value_objects;

use app\domain\course\value_objects\Workload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Workload Value Object.
 */
class WorkloadTest extends TestCase
{
	public function test_valid_workload(): void
	{
		$workload = new Workload(60);
		$this->assertSame(60, $workload->get_hours());
		$this->assertSame('60', (string) $workload);
	}

	public function test_negative_workload_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new Workload(-5);
	}

	public function test_workload_equality(): void
	{
		$workload1 = new Workload(40);
		$workload2 = new Workload(40);
		$workload3 = new Workload(80);

		$this->assertTrue($workload1->equals($workload2));
		$this->assertFalse($workload1->equals($workload3));
	}
}

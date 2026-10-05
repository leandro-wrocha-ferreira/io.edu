<?php

namespace tests\unit\domain\course\value_objects;

use app\domain\course\value_objects\CourseStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CourseStatus Value Object.
 */
class CourseStatusTest extends TestCase
{
	public function test_draft_status(): void
	{
		$status = CourseStatus::draft();
		$this->assertTrue($status->is_draft());
		$this->assertFalse($status->is_active());
		$this->assertFalse($status->is_archived());
		$this->assertSame(CourseStatus::DRAFT, (string) $status);
	}

	public function test_active_status(): void
	{
		$status = CourseStatus::active();
		$this->assertTrue($status->is_active());
		$this->assertFalse($status->is_draft());
		$this->assertFalse($status->is_archived());
		$this->assertSame(CourseStatus::ACTIVE, (string) $status);
	}

	public function test_archived_status(): void
	{
		$status = CourseStatus::archived();
		$this->assertTrue($status->is_archived());
		$this->assertFalse($status->is_draft());
		$this->assertFalse($status->is_active());
		$this->assertSame(CourseStatus::ARCHIVED, (string) $status);
	}

	public function test_invalid_status_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseStatus('published');
	}

	public function test_status_equality(): void
	{
		$status1 = new CourseStatus('active');
		$status2 = CourseStatus::active();
		$status3 = CourseStatus::draft();

		$this->assertTrue($status1->equals($status2));
		$this->assertFalse($status1->equals($status3));
	}
}

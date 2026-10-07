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
		$this->assertFalse($status->is_inactive());
		$this->assertFalse($status->is_archived());
		$this->assertSame(CourseStatus::DRAFT, (string) $status);
	}

	public function test_active_status(): void
	{
		$status = CourseStatus::active();
		$this->assertTrue($status->is_active());
		$this->assertFalse($status->is_draft());
		$this->assertFalse($status->is_inactive());
		$this->assertFalse($status->is_archived());
		$this->assertSame(CourseStatus::ACTIVE, (string) $status);
	}

	public function test_inactive_status(): void
	{
		$status = CourseStatus::inactive();
		$this->assertTrue($status->is_inactive());
		$this->assertFalse($status->is_draft());
		$this->assertFalse($status->is_active());
		$this->assertFalse($status->is_archived());
		$this->assertSame(CourseStatus::INACTIVE, (string) $status);
	}

	public function test_archived_status(): void
	{
		$status = CourseStatus::archived();
		$this->assertTrue($status->is_archived());
		$this->assertFalse($status->is_draft());
		$this->assertFalse($status->is_active());
		$this->assertFalse($status->is_inactive());
		$this->assertSame(CourseStatus::ARCHIVED, (string) $status);
	}

	public function test_transition_rules(): void
	{
		$draft = CourseStatus::draft();
		$active = CourseStatus::active();
		$inactive = CourseStatus::inactive();
		$archived = CourseStatus::archived();

		// Draft can transition to anything
		$this->assertTrue($draft->can_transition_to($active));
		$this->assertTrue($draft->can_transition_to($inactive));
		$this->assertTrue($draft->can_transition_to($archived));
		$this->assertTrue($draft->can_transition_to($draft));

		// Non-draft cannot transition back to draft
		$this->assertFalse($active->can_transition_to($draft));
		$this->assertFalse($inactive->can_transition_to($draft));
		$this->assertFalse($archived->can_transition_to($draft));

		// Non-draft can transition between active, inactive, archived
		$this->assertTrue($active->can_transition_to($inactive));
		$this->assertTrue($active->can_transition_to($archived));
		$this->assertTrue($inactive->can_transition_to($active));
		$this->assertTrue($inactive->can_transition_to($archived));
		$this->assertTrue($archived->can_transition_to($active));
		$this->assertTrue($archived->can_transition_to($inactive));
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

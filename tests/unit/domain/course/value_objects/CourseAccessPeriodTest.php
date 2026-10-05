<?php

namespace tests\unit\domain\course\value_objects;

use app\domain\course\value_objects\CourseAccessPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CourseAccessPeriod Value Object.
 */
class CourseAccessPeriodTest extends TestCase
{
	public function test_lifetime_access(): void
	{
		$period = CourseAccessPeriod::lifetime();
		$this->assertTrue($period->is_lifetime());
		$this->assertFalse($period->is_limited_time());
		$this->assertNull($period->get_days());
		$this->assertSame('lifetime', (string) $period);
	}

	public function test_limited_time_access(): void
	{
		$period = CourseAccessPeriod::limited_time(365);
		$this->assertTrue($period->is_limited_time());
		$this->assertFalse($period->is_lifetime());
		$this->assertSame(365, $period->get_days());
		$this->assertSame('limited_time:365', (string) $period);
	}

	public function test_limited_time_with_zero_or_negative_days_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIMITED_TIME, 0);
	}

	public function test_limited_time_with_negative_days_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIMITED_TIME, -10);
	}

	public function test_limited_time_with_null_days_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIMITED_TIME, null);
	}

	public function test_invalid_type_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseAccessPeriod('annual', 365);
	}

	public function test_equality(): void
	{
		$period1 = CourseAccessPeriod::limited_time(180);
		$period2 = new CourseAccessPeriod('limited_time', 180);
		$period3 = CourseAccessPeriod::limited_time(365);
		$period4 = CourseAccessPeriod::lifetime();

		$this->assertTrue($period1->equals($period2));
		$this->assertFalse($period1->equals($period3));
		$this->assertFalse($period1->equals($period4));
	}
}

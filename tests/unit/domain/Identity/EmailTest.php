<?php

use app\domain\identity\Email;

/**
 * Test suite for Email Value Object.
 *
 * Covers creation, normalization (lowercase, trim),
 * validation, and equality checks.
 */
class EmailTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test creating a valid email address.
     *
     * @return void
     */
    public function test_create_valid_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that email is normalized to lowercase.
     *
     * @return void
     */
    public function test_create_email_lowercase()
    {
        $email = new Email('TEST@EXAMPLE.COM');
        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that email is trimmed of whitespace.
     *
     * @return void
     */
    public function test_create_email_trimmed()
    {
        $email = new Email('  test@example.com  ');
        $this->assertEquals('test@example.com', (string) $email);
    }

    /**
     * Test that an invalid email format throws an exception.
     *
     * @return void
     */
    public function test_invalid_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid-email');
    }

    /**
     * Test that an empty email throws an exception.
     *
     * @return void
     */
    public function test_empty_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('');
    }

    /**
     * Test that an email without @ throws an exception.
     *
     * @return void
     */
    public function test_email_without_at_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('testexample.com');
    }

    /**
     * Test that two equal emails are identified as equal.
     *
     * @return void
     */
    public function test_email_equals()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    /**
     * Test that two different emails are not equal.
     *
     * @return void
     */
    public function test_email_not_equals()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('other@example.com');
        $this->assertFalse($email1->equals($email2));
    }
}

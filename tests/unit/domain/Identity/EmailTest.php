<?php

use app\domain\identity\Email;

class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function test_create_valid_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_create_email_lowercase()
    {
        $email = new Email('TEST@EXAMPLE.COM');
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_create_email_trimmed()
    {
        $email = new Email('  test@example.com  ');
        $this->assertEquals('test@example.com', (string) $email);
    }

    public function test_invalid_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid-email');
    }

    public function test_empty_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('');
    }

    public function test_email_without_at_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('testexample.com');
    }

    public function test_email_equals()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    public function test_email_not_equals()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('other@example.com');
        $this->assertFalse($email1->equals($email2));
    }
}

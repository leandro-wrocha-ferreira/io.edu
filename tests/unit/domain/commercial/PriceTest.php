<?php

namespace tests\unit\domain\commercial;

use app\domain\commercial\value_objects\Price;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Price Value Object.
 */
class PriceTest extends TestCase
{
	public function test_from_cents_success(): void
	{
		$price = Price::from_cents(19990);
		$this->assertSame(19990, $price->get_cents());
		$this->assertSame('BRL', $price->get_currency());
		$this->assertFalse($price->is_zero());
		$this->assertSame('19990', (string) $price);
		$this->assertSame('R$ 199,90', $price->format());
		$this->assertSame(199.9, $price->to_float_display());
	}

	public function test_zero_price_is_free(): void
	{
		$price = Price::from_cents(0);
		$this->assertTrue($price->is_zero());
		$this->assertSame('Gratuito', $price->format());
	}

	public function test_negative_cents_throws_validation_exception(): void
	{
		$this->expectException(ValidationException::class);
		Price::from_cents(-100);
	}

	public function test_from_string_brazilian_format(): void
	{
		$price = Price::from_string('199,90');
		$this->assertSame(19990, $price->get_cents());

		$thousand = Price::from_string('1.250,50');
		$this->assertSame(125050, $thousand->get_cents());

		$symbol = Price::from_string('R$ 49,00');
		$this->assertSame(4900, $symbol->get_cents());
	}

	public function test_from_string_dot_decimal_format(): void
	{
		$price = Price::from_string('99.50');
		$this->assertSame(9950, $price->get_cents());

		$thousand = Price::from_string('1,500.00');
		$this->assertSame(150000, $thousand->get_cents());
	}

	public function test_from_string_whole_number(): void
	{
		$price = Price::from_string('150');
		$this->assertSame(15000, $price->get_cents());
	}

	public function test_equals(): void
	{
		$price1 = Price::from_cents(5000);
		$price2 = Price::from_cents(5000);
		$price3 = Price::from_cents(6000);

		$this->assertTrue($price1->equals($price2));
		$this->assertFalse($price1->equals($price3));
	}
}

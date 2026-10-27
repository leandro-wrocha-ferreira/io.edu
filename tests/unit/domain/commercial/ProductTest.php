<?php

namespace tests\unit\domain\commercial;

use app\domain\commercial\Product;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Product domain entity and commercial invariants.
 */
class ProductTest extends TestCase
{
	public function test_free_product_invariants(): void
	{
		$product = Product::create(
			10,
			true,
			null,
			BillingModel::subscription(), // attempted subscription on free
			InstallmentLimit::from_int(12), // attempted 12 installments on free
			SubscriptionInterval::monthly(),
			12,
			new EnabledPaymentMethods(['credit_card']),
			1, // attempted financial profile
			2
		);

		$this->assertTrue($product->is_free());
		$this->assertNull($product->get_price());
		$this->assertTrue($product->get_billing_model()->is_one_time());
		$this->assertSame(1, $product->get_max_installments()->get_limit());
		$this->assertNull($product->get_subscription_interval());
		$this->assertNull($product->get_max_billing_cycles());
		$this->assertTrue($product->get_payment_methods()->is_empty());
		$this->assertNull($product->get_payment_configuration_id());
		$this->assertSame(2, $product->get_cost_center_id());
	}

	public function test_paid_product_invariants(): void
	{
		$product = Product::create(
			10,
			false,
			Price::from_cents(19900),
			BillingModel::one_time(),
			InstallmentLimit::from_int(10),
			null,
			null,
			new EnabledPaymentMethods(['pix', 'credit_card']),
			3,
			4
		);

		$this->assertFalse($product->is_free());
		$this->assertNotNull($product->get_price());
		$this->assertSame(19900, $product->get_price()->get_cents());
		$this->assertSame(10, $product->get_max_installments()->get_limit());
		$this->assertSame(3, $product->get_payment_configuration_id());
		$this->assertSame(4, $product->get_cost_center_id());
	}

	public function test_subscription_requires_interval(): void
	{
		$this->expectException(ValidationException::class);
		Product::create(
			10,
			false,
			Price::from_cents(5000),
			BillingModel::subscription(),
			InstallmentLimit::single(),
			null // missing interval
		);
	}

	public function test_sales_eligibility_validation_passes_for_free(): void
	{
		$product = Product::create(10, true, null);
		$product->validate_sales_eligibility();
		$this->assertTrue(true);
	}

	public function test_sales_eligibility_fails_when_paid_without_configuration(): void
	{
		$product = Product::create(
			10,
			false,
			Price::from_cents(9900),
			BillingModel::one_time(),
			InstallmentLimit::single(),
			null,
			null,
			new EnabledPaymentMethods(['pix']),
			null // missing config
		);

		$this->expectException(ValidationException::class);
		$product->validate_sales_eligibility();
	}

	public function test_sales_eligibility_fails_when_paid_without_payment_methods(): void
	{
		$product = Product::create(
			10,
			false,
			Price::from_cents(9900),
			BillingModel::one_time(),
			InstallmentLimit::single(),
			null,
			null,
			new EnabledPaymentMethods([]), // no payment methods
			1
		);

		$this->expectException(ValidationException::class);
		$product->validate_sales_eligibility();
	}

	public function test_paid_product_requires_valid_price(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Paid products must have a valid non-zero price');

		Product::create(10, false, null);
	}
}

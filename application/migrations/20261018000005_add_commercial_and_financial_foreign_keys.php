<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_commercial_and_financial_foreign_keys extends CI_Migration
{
	public function up()
	{
		$this->db->query("ALTER TABLE `payment_accounts`
			ADD CONSTRAINT `fk_pay_acc_provider_credential` 
			FOREIGN KEY (`provider_id`, `credential_id`) 
			REFERENCES `payment_credentials` (`provider_id`, `id`) 
			ON DELETE RESTRICT");

		$this->db->query("ALTER TABLE `payment_configurations`
			ADD CONSTRAINT `fk_pay_cfg_account` 
			FOREIGN KEY (`payment_account_id`) 
			REFERENCES `payment_accounts` (`id`) 
			ON DELETE RESTRICT");

		$this->db->query("ALTER TABLE `products`
			ADD CONSTRAINT `fk_prod_course` 
			FOREIGN KEY (`course_id`) 
			REFERENCES `courses` (`id`) 
			ON DELETE RESTRICT ON UPDATE CASCADE,
			ADD CONSTRAINT `fk_prod_payment_config` 
			FOREIGN KEY (`payment_configuration_id`) 
			REFERENCES `payment_configurations` (`id`) 
			ON DELETE RESTRICT ON UPDATE CASCADE,
			ADD CONSTRAINT `fk_prod_cost_center` 
			FOREIGN KEY (`cost_center_id`) 
			REFERENCES `cost_centers` (`id`) 
			ON DELETE SET NULL ON UPDATE CASCADE");

		$this->db->query("ALTER TABLE `product_price_versions`
			ADD CONSTRAINT `fk_ver_product` 
			FOREIGN KEY (`product_id`) 
			REFERENCES `products` (`id`) 
			ON DELETE RESTRICT ON UPDATE CASCADE,
			ADD CONSTRAINT `fk_ver_user` 
			FOREIGN KEY (`created_by`) 
			REFERENCES `users` (`id`) 
			ON DELETE SET NULL ON UPDATE CASCADE");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE `product_price_versions`
			DROP FOREIGN KEY `fk_ver_user`,
			DROP FOREIGN KEY `fk_ver_product`");

		$this->db->query("ALTER TABLE `products`
			DROP FOREIGN KEY `fk_prod_cost_center`,
			DROP FOREIGN KEY `fk_prod_payment_config`,
			DROP FOREIGN KEY `fk_prod_course`");

		$this->db->query("ALTER TABLE `payment_configurations`
			DROP FOREIGN KEY `fk_pay_cfg_account`");

		$this->db->query("ALTER TABLE `payment_accounts`
			DROP FOREIGN KEY `fk_pay_acc_provider_credential`");
	}
}

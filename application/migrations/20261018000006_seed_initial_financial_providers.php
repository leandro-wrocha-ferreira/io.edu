<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_initial_financial_providers extends CI_Migration
{
	public function up()
	{
		$providers = [
			[
				'name' => 'Asaas',
				'slug' => 'asaas',
				'supported_methods' => json_encode(['pix', 'credit_card', 'boleto']),
				'is_active' => 1,
			],
			[
				'name' => 'Pagar.me',
				'slug' => 'pagarme',
				'supported_methods' => json_encode(['pix', 'credit_card', 'boleto']),
				'is_active' => 0,
			],
			[
				'name' => 'Mercado Pago',
				'slug' => 'mercadopago',
				'supported_methods' => json_encode(['pix', 'credit_card', 'boleto']),
				'is_active' => 0,
			],
		];

		foreach ($providers as $provider) {
			$this->db->insert('payment_providers', $provider);
		}
	}

	public function down()
	{
		$this->db->where_in('slug', ['asaas', 'pagarme', 'mercadopago']);
		$this->db->delete('payment_providers');
	}
}

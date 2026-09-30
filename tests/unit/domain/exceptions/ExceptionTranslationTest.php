<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for Exception Language Catalogs and Translation Mapping.
 */
class ExceptionTranslationTest extends TestCase
{
	/**
	 * Test English exception language file exists and has valid mappings.
	 *
	 * @return void
	 */
	public function test_english_exception_catalog(): void
	{
		$language_file = FCPATH . 'application/language/english/exceptions_lang.php';
		$this->assertFileExists($language_file);

		$lang = [];
		require $language_file;

		$this->assertNotEmpty($lang);
		$this->assertEquals('User not found', $lang['exception_user_not_found']);
		$this->assertEquals('Role not found', $lang['exception_role_not_found']);
		$this->assertEquals('Email is already in use', $lang['exception_email_in_use']);
		$this->assertEquals('Default system roles cannot be deleted', $lang['exception_default_roles_cannot_be_deleted']);
		$this->assertEquals('The AdminMaster role is protected and cannot be modified', $lang['exception_admin_master_protected']);
		$this->assertEquals('You cannot alter the status of your own user', $lang['exception_cannot_alter_own_status']);
		$this->assertEquals('You cannot delete your own user', $lang['exception_cannot_delete_own_user']);
		$this->assertEquals('You cannot edit your own user on this screen', $lang['exception_cannot_edit_own_user']);
		$this->assertEquals('Invalid credentials', $lang['exception_invalid_credentials']);

		// Phrase mappings
		$this->assertEquals('User not found', $lang['User not found']);
		$this->assertEquals('Role not found', $lang['Role not found']);
		$this->assertEquals('Email is already in use', $lang['Email is already in use']);
	}

	/**
	 * Test Portuguese Brazilian exception language file exists and has valid translations.
	 *
	 * @return void
	 */
	public function test_portuguese_brazilian_exception_catalog(): void
	{
		$language_file = FCPATH . 'application/language/portuguese-brazilian/exceptions_lang.php';
		$this->assertFileExists($language_file);

		$lang = [];
		require $language_file;

		$this->assertNotEmpty($lang);
		$this->assertEquals('Usuário não encontrado', $lang['exception_user_not_found']);
		$this->assertEquals('Perfil não encontrado', $lang['exception_role_not_found']);
		$this->assertEquals('E-mail já está em uso', $lang['exception_email_in_use']);
		$this->assertEquals('Perfis padrão do sistema não podem ser excluídos.', $lang['exception_default_roles_cannot_be_deleted']);
		$this->assertEquals('O perfil AdminMaster é protegido e não pode ser alterado.', $lang['exception_admin_master_protected']);
		$this->assertEquals('Você não pode alterar o status do seu próprio usuário.', $lang['exception_cannot_alter_own_status']);
		$this->assertEquals('Você não pode excluir o seu próprio usuário.', $lang['exception_cannot_delete_own_user']);
		$this->assertEquals('Você não pode editar ou alterar as configurações do seu próprio usuário nesta tela.', $lang['exception_cannot_edit_own_user']);
		$this->assertEquals('Credenciais inválidas', $lang['exception_invalid_credentials']);

		// Phrase mappings
		$this->assertEquals('Usuário não encontrado', $lang['User not found']);
		$this->assertEquals('Perfil não encontrado', $lang['Role not found']);
		$this->assertEquals('E-mail já está em uso', $lang['Email is already in use']);
	}

	/**
	 * Test translation resolution logic for Brazilian user (PT-BR) vs international user (EN).
	 *
	 * @return void
	 */
	public function test_translation_resolution_by_locale(): void
	{
		$lang = [];
		require FCPATH . 'application/language/english/exceptions_lang.php';
		$en_lang = $lang;

		$lang = [];
		require FCPATH . 'application/language/portuguese-brazilian/exceptions_lang.php';
		$pt_lang = $lang;

		$resolve_message = function (string $message, string $locale) use ($en_lang, $pt_lang): string {
			$catalog = ($locale === 'portuguese-brazilian') ? $pt_lang : $en_lang;

			if (isset($catalog[$message])) {
				return $catalog[$message];
			}

			$normalized_key = 'exception_' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($message)));
			if (isset($catalog[$normalized_key])) {
				return $catalog[$normalized_key];
			}

			return $message;
		};

		// User from Brazil gets PT-BR
		$this->assertEquals('Usuário não encontrado', $resolve_message('User not found', 'portuguese-brazilian'));
		$this->assertEquals('Perfil não encontrado', $resolve_message('Role not found', 'portuguese-brazilian'));
		$this->assertEquals('E-mail já está em uso', $resolve_message('Email is already in use', 'portuguese-brazilian'));
		$this->assertEquals('Usuário não encontrado', $resolve_message('exception_user_not_found', 'portuguese-brazilian'));

		// User from any other country (US, UK, ES, etc.) gets EN
		$this->assertEquals('User not found', $resolve_message('User not found', 'english'));
		$this->assertEquals('Role not found', $resolve_message('Role not found', 'english'));
		$this->assertEquals('Email is already in use', $resolve_message('Email is already in use', 'english'));
		$this->assertEquals('User not found', $resolve_message('exception_user_not_found', 'english'));

		// Unknown message falls back to original
		$this->assertEquals('Custom business error', $resolve_message('Custom business error', 'portuguese-brazilian'));
		$this->assertEquals('Custom business error', $resolve_message('Custom business error', 'english'));
	}
}

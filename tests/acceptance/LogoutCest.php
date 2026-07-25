<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

/**
 * E2E tests for logout flow.
 *
 * Covers admin and student logout after authentication.
 */
class LogoutCest
{
    /**
     * Verify admin can log out and is redirected to login.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function adminCanLogout(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'admin@example.com');
        $I->fillField('#password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin/painel');

        $I->click('Sair');
        $I->seeCurrentUrlEquals('/autenticacao/login');
    }

    /**
     * Verify student can log out and is redirected to login.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function studentCanLogout(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'student@example.com');
        $I->fillField('#password', 'student123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/aluno/painel');

        $I->click('Sair');
        $I->seeCurrentUrlEquals('/autenticacao/login');
    }
}

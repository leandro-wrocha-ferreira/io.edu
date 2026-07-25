<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

/**
 * E2E tests for the login flow.
 *
 * Covers page loading, invalid credentials,
 * and successful login for admin and student roles.
 */
class LoginCest
{
    /**
     * Verify login page loads with all required elements.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function loginPageLoadsCorrectly(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->see('Login');
        $I->seeElement('#email');
        $I->seeElement('#password');
        $I->seeElement('button[type="submit"]');
    }

    /**
     * Verify invalid credentials display an error message.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function loginWithInvalidCredentialsShowsError(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'wrong@example.com');
        $I->fillField('#password', 'wrongpass');
        $I->click('button[type="submit"]');
        $I->see('Invalid credentials');
    }

    /**
     * Verify admin users are redirected to the admin panel.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function loginWithValidAdminCredentialsRedirectsToDashboard(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'admin@example.com');
        $I->fillField('#password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin/painel');
        $I->see('Painel Administrativo');
    }

    /**
     * Verify student users are redirected to the student panel.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function loginWithValidStudentCredentialsRedirectsToDashboard(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'student@example.com');
        $I->fillField('#password', 'student123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/aluno/painel');
        $I->see('Painel do Aluno');
    }
}

<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

class LoginCest
{
    public function loginPageLoadsCorrectly(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->see('Login');
        $I->seeElement('#email');
        $I->seeElement('#password');
        $I->seeElement('button[type="submit"]');
    }

    public function loginWithInvalidCredentialsShowsError(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'wrong@example.com');
        $I->fillField('#password', 'wrongpass');
        $I->click('button[type="submit"]');
        $I->see('Credenciais inválidas');
    }

    public function loginWithValidAdminCredentialsRedirectsToDashboard(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'admin@example.com');
        $I->fillField('#password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin/painel');
        $I->see('Painel Administrativo');
    }

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

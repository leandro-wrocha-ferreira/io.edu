<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

class DashboardCest
{
    public function unauthenticatedUserIsRedirectedToLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/painel');
        $I->seeCurrentUrlEquals('/autenticacao/login');
    }

    public function adminCanAccessDashboard(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'admin@example.com');
        $I->fillField('#password', 'admin123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/admin/painel');
        $I->see('Painel Administrativo');
        $I->see('Olá');
    }

    public function studentCanAccessDashboard(AcceptanceTester $I)
    {
        $I->amOnPage('/autenticacao/login');
        $I->fillField('#email', 'student@example.com');
        $I->fillField('#password', 'student123');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/aluno/painel');
        $I->see('Painel do Aluno');
        $I->see('Olá');
    }
}

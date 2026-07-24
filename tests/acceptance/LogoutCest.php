<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

class LogoutCest
{
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

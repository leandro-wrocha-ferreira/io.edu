<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

/**
 * E2E tests for dashboard access control.
 *
 * Covers unauthenticated redirect, and successful
 * dashboard access for admin and student roles.
 */
class DashboardCest
{
    /**
     * Verify unauthenticated users are redirected to login.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function unauthenticatedUserIsRedirectedToLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/painel');
        $I->seeCurrentUrlEquals('/autenticacao/login');
    }

    /**
     * Verify admin can access the admin dashboard.
     *
     * @param AcceptanceTester $I
     * @return void
     */
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

    /**
     * Verify student can access the student dashboard.
     *
     * @param AcceptanceTester $I
     * @return void
     */
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

<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\usecases\admin\ActivateUserUseCase;
use app\domain\exceptions\NotFoundException;

use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for ActivateUserUseCase.
 */
class ActivateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockUserRepository */
    private $mock_user_repository;

    /**
     * Set up test environment.
     *
     * Creates a fresh MockUserRepository before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mock_user_repository = new MockUserRepository();
    }

    /**
     * Test successful user activation.
     *
     * @return void
     */
    public function test_activate_user_success()
    {
        $email = new Email('inactive@example.com');
        $user = User::create('Inactive User', $email, 'password123');
        $user->set_active(false); // Ensure the user is inactive initially
        
        
        $user = $this->mock_user_repository->save($user);
        $use_case = new ActivateUserUseCase($this->mock_user_repository);
        $user = $use_case->execute($user->get_id());
        $this->assertTrue($user->is_active());
    }

    /**
     * Test activation throws exception for non-existing user.
     *
     * @return void
     */
    public function test_activate_user_not_found_throws_exception()
    {
        $use_case = new ActivateUserUseCase($this->mock_user_repository);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not found');
        
        $use_case->execute(999);
    }
}

<?php

namespace tests\unit\infrastructure\events;

use app\infrastructure\events\ModelEventDispatcher;
use app\infrastructure\events\ModelObserverInterface;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ModelEventDispatcher.
 */
class ModelEventDispatcherTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		ModelEventDispatcher::clear();
	}

	protected function tearDown(): void
	{
		ModelEventDispatcher::clear();
		parent::tearDown();
	}

	public function test_register_and_dispatch_event_to_observer(): void
	{
		$observer = new class implements ModelObserverInterface {
			/** @var array<array<string, mixed>> */
			public array $handled_events = [];

			public function handle(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void
			{
				$this->handled_events[] = [
					'action'     => $action,
					'table'      => $table,
					'identifier' => $identifier,
					'before'     => $before,
					'after'      => $after,
				];
			}
		};

		ModelEventDispatcher::register($observer);
		$this->assertTrue(ModelEventDispatcher::has_observers());

		ModelEventDispatcher::dispatch('insert', 'users', 42, null, ['name' => 'Alice']);

		$this->assertCount(1, $observer->handled_events);
		$this->assertSame('insert', $observer->handled_events[0]['action']);
		$this->assertSame('users', $observer->handled_events[0]['table']);
		$this->assertSame(42, $observer->handled_events[0]['identifier']);
		$this->assertNull($observer->handled_events[0]['before']);
		$this->assertSame(['name' => 'Alice'], $observer->handled_events[0]['after']);
	}

	public function test_unregister_removes_specific_observer(): void
	{
		$observer = new class implements ModelObserverInterface {
			public function handle(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void {}
		};

		ModelEventDispatcher::register($observer);
		$this->assertTrue(ModelEventDispatcher::has_observers());

		ModelEventDispatcher::unregister(get_class($observer));
		$this->assertFalse(ModelEventDispatcher::has_observers());
	}

	public function test_clear_removes_all_observers(): void
	{
		$observer1 = new class implements ModelObserverInterface {
			public function handle(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void {}
		};

		ModelEventDispatcher::register($observer1);
		$this->assertTrue(ModelEventDispatcher::has_observers());

		ModelEventDispatcher::clear();
		$this->assertFalse(ModelEventDispatcher::has_observers());
	}
}

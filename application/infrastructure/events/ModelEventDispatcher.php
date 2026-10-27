<?php

namespace app\infrastructure\events;

/**
 * Lightweight dispatcher for model lifecycle events.
 */
class ModelEventDispatcher
{
	/**
	 * Registered observers.
	 *
	 * @var array<string, ModelObserverInterface>
	 */
	private static array $observers = [];

	/**
	 * Register an observer.
	 *
	 * @param ModelObserverInterface $observer
	 * @return void
	 */
	public static function register(ModelObserverInterface $observer): void
	{
		$class_name = get_class($observer);
		self::$observers[$class_name] = $observer;
	}

	/**
	 * Unregister an observer by its class name.
	 *
	 * @param string $observer_class
	 * @return void
	 */
	public static function unregister(string $observer_class): void
	{
		unset(self::$observers[$observer_class]);
	}

	/**
	 * Clear all registered observers.
	 *
	 * @return void
	 */
	public static function clear(): void
	{
		self::$observers = [];
	}

	/**
	 * Check if any observers are registered.
	 *
	 * @return bool
	 */
	public static function has_observers(): bool
	{
		return !empty(self::$observers);
	}

	/**
	 * Dispatch an event to all registered observers.
	 *
	 * @param string $action Database action (e.g. 'insert', 'update', 'destroy')
	 * @param string $table Database table name
	 * @param int|string|null $identifier Row identifier or condition summary
	 * @param array|null $before Data before operation
	 * @param array|null $after Data after operation
	 * @return void
	 */
	public static function dispatch(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void
	{
		foreach (self::$observers as $observer) {
			$observer->handle($action, $table, $identifier, $before, $after);
		}
	}
}

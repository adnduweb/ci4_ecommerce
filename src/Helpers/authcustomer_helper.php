<?php

use Config\Services;

if (!function_exists('customer_logged_in')) {
	/**
	 * Checks to see if the user is logged in.
	 *
	 * 
	 * @return bool
	 */
	function customer_logged_in()
	{
		return Services::authenticationcustomer()->check();
	}
}

if (!function_exists('customer')) {
	/**
	 * Returns the customer instance for the current logged in customer.
	 *
	 * @return \Myth\Auth\Entities\Customer|null
	 */
	function customer()
	{
		$authenticate = Services::authenticationcustomer();
		$authenticate->check();
		return $authenticate->customer();
	}
}

if (!function_exists('customer_id')) {
	/**
	 * Returns the User ID for the current logged in user.
	 *
	 * @return \Myth\Auth\Entities\Customer|null
	 */
	function customer_id()
	{
		$authenticate = Services::authenticationcustomer();
		$authenticate->check();
		return $authenticate->id();
	}
}

if (!function_exists('in_groups')) {
	/**
	 * Ensures that the current user is in at least one of the passed in
	 * groups. The groups can be passed in as either ID's or group names.
	 * You can pass either a single item or an array of items.
	 *
	 * Example:
	 *  in_groups([1, 2, 3]);
	 *  in_groups(14);
	 *  in_groups('admins');
	 *  in_groups( ['admins', 'moderators'] );
	 *
	 * @param mixed  $groups
	 *
	 * @return bool
	 */
	function in_groups($groups): bool
	{
		$authenticate = Services::authenticationcustomer();
		$authorize    = Services::authorizationcustomer();

		if ($authenticate->check()) {
			return $authorize->inGroup($groups, $authenticate->id());
		}

		return false;
	}
}

if (!function_exists('has_permission')) {
	/**
	 * Ensures that the current user has the passed in permission.
	 * The permission can be passed in either as an ID or name.
	 *
	 * @param int|string $permission
	 *
	 * @return bool
	 */
	function has_permission($permission): bool
	{
		$authenticate = Services::authenticationcustomer();
		$authorize    = Services::authorizationcustomer();

		if ($authenticate->check()) {
			return $authorize->hasPermission($permission, $authenticate->id()) ?? false;
		}

		return false;
	}
}

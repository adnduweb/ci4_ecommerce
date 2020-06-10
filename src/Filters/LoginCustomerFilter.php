<?php

namespace Adnduweb\Ci4_ecommerce\Filters;

use Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LoginCustomerFilter implements FilterInterface
{
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface $request
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request)
	{

		if (!function_exists('customer_logged_in')) {
			helper('authCustomer');
		}

		$current = (string) current_url(true)
			->setHost('')
			->setScheme('')
			->stripQuery('token');

		// Make sure this isn't already a login route
		if (in_array((string) $current, [route_to('login'), route_to('forgot'), route_to('reset-password'), route_to('register')])) {
			return;
		}

		// if no user is logged in then send to the login form
		$authenticate = Services::authenticationCustomer();
		if (!$authenticate->check()) {
			session()->set('redirect_url', current_url());
			return redirect('signin');
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Allows After filters to inspect and modify the response
	 * object as needed. This method does not allow any way
	 * to stop execution of other after filters, short of
	 * throwing an Exception or Error.
	 *
	 * @param \CodeIgniter\HTTP\RequestInterface  $request
	 * @param \CodeIgniter\HTTP\ResponseInterface $response
	 *
	 * @return mixed
	 */
	public function after(RequestInterface $request, ResponseInterface $response)
	{
	}

	//--------------------------------------------------------------------
}

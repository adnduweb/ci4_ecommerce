<?php

namespace Adnduweb\Ci4_ecommerce\Authentication;

use Config\App;
use CodeIgniter\Events\Events;
use CodeIgniter\Model;
use Michalsn\Uuid\UuidModel;
use Config\Services;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Exceptions\AuthException;
use Adnduweb\Ci4_ecommerce\Exceptions\CustomerNotFoundException;

class AuthenticationBase
{
    /**
     * @var User
     */
    protected $customer;

    /**
     * @var Model
     */
    protected $customerModel;

    /**
     * @var Model
     */
    protected $loginModel;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var \Config\AuthCustomer
     */
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Returns the current error, if any.
     *
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Whether to continue instead of throwing exceptions,
     * as defined in config.
     *
     * @return string
     */
    public function silent()
    {
        return $this->config->silent;
    }


    /**
     * Logs a user into the system.
     * NOTE: does not perform validation. All validation should
     * be done prior to using the login method.
     *
     * @param \Adnduweb\Ci4_ecommerce\Entities\Customer $customer
     * @param bool                     $remember
     *
     * @return bool
     * @throws \Exception
     */
    public function login(Customer $customer = null, bool $remember = false): bool
    {
        if (empty($customer)) {
            $this->customer = null;
            return false;
        }

        $this->customer = $customer;

        // Always record a login attempt
        $ipAddress = Services::request()->getIPAddress();
        $this->recordLoginAttempt($customer->email, $ipAddress, $customer->id ?? null, true);

        // Regenerate the session ID to help protect against session fixation
        if (ENVIRONMENT !== 'testing') {
            session()->regenerate();
        }

        // Let the session know we're logged in
        session()->set('customer_logged_in', $this->customer->id);

        // Unique
        // Hash password
        $salt = uniqid(mt_rand(), true);
        $hashed_token = hash('sha1', date("Y-m-d H:i:s", time()) . $salt);
        $session_id = session_id();

        $newdata = array(
            'customer_token'      => $hashed_token,
            'customer_session_id' => $session_id,
            'customer_logged_in' . $session_id         => TRUE,
        );
        session()->set($newdata);


        // When logged in, ensure cache control headers are in place
        Services::response()->noCache();

        if ($remember && $this->config->allowRemembering) {
            $this->rememberCustomer($this->customer->id);
        }

        // We'll give a 20% chance to need to do a purge since we
        // don't need to purge THAT often, it's just a maintenance issue.
        // to keep the table from getting out of control.
        if (mt_rand(1, 100) < 20) {
            $this->loginModel->purgeOldRememberTokens();
        }

        // trigger login event, in case anyone cares
        Events::trigger('login', $customer);

        return true;
    }

    /**
     * Checks to see if the customer is logged in.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        // On the off chance
        if ($this->customer instanceof Customer) {
            return true;
        }

        if ($customerID = session('customer_logged_in')) {
            // Store our current user object
            $this->customer = $this->customerModel->find($customerID);

            return $this->customer instanceof Customer;
        }

        return false;
    }


    /**
     * Logs a user into the system by their ID.
     *
     * @param int  $id
     * @param bool $remember
     */
    public function loginByID(int $id, bool $remember = false)
    {
        $customer = $this->retrieveCustomer(['id' => $id]);

        if (empty($customer)) {
            throw CustomerNotFoundException::forCustomerID($id);
        }

        return $this->login($customer, $remember);
    }

    /**
     * Logs a user out of the system.
     */
    public function logout()
    {
        helper('cookie');

        $customer = $this->customer();

        // Destroy the session data - but ensure a session is still
        // available for flash messages, etc.
        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                $_SESSION[$key] = NULL;
                unset($_SESSION[$key]);
            }
        }

        // Regenerate the session ID for a touch of added safety.
        session()->regenerate(true);

        // Take care of any remember me functionality
        $this->loginModel->purgeRememberTokens($customer->id);

        // Remove the cookie
        delete_cookie("remember_f");

        // trigger logout event
        Events::trigger('logout', $customer);
    }

    /**
     * Record a login attempt
     *
     * @param string      $email
     * @param string|null $ipAddress
     * @param int|null    $customerID
     *
     * @param bool        $success
     *
     * @return bool|int|string
     */
    public function recordLoginAttempt(string $email, string $ipAddress = null, int $customerID = null, bool $success)
    {
        return $this->loginModel->insert([
            'ip_address' => $ipAddress,
            'email' => $email,
            'customer_id' => $customerID,
            'date' => date('Y-m-d H:i:s'),
            'success' => (int) $success
        ]);
    }

    /**
     * Generates a timing-attack safe remember me token
     * and stores the necessary info in the db and a cookie.
     *
     * @see https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
     *
     * @param int $customerID
     *
     * @throws \Exception
     */
    public function rememberCustomer(int $customerID)
    {
        $selector  = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(20));
        $expires   = date('Y-m-d H:i:s', time() + $this->config->rememberLength);

        $token = $selector . ':' . $validator;

        // Store it in the database
        $this->loginModel->rememberCustomer($customerID, $selector, hash('sha256', $validator), $expires);

        // Save it to the user's browser in a cookie.
        $appConfig = config('App');
        $response = \Config\Services::response();

        // Create the cookie
        $response->setCookie(
            'remember_f',                                  // Cookie Name
            $token,                                     // Value
            $this->config->rememberLength,              // # Seconds until it expires
            $appConfig->cookieDomain,
            $appConfig->cookiePath,
            $appConfig->cookiePrefix,
            $appConfig->cookieHTTPOnly,                 // Only send over HTTPS?
            true                                        // Hide from Javascript?
        );
    }

    /**
     * Sets a new validator for this user/selector. This allows
     * a one-time use of remember-me tokens, but still allows
     * a user to be remembered on multiple browsers/devices.
     *
     * @param int    $customerID
     * @param string $selector
     */
    public function refreshRemember(int $customerID, string $selector)
    {
        $existing = $this->loginModel->getRememberToken($selector);

        // No matching record? Shouldn't happen, but remember the user now.
        if (empty($existing)) {
            return $this->rememberCustomer($customerID);
        }

        // Update the validator in the database and the session
        $validator = bin2hex(random_bytes(20));

        $this->loginModel->updateRememberValidator($selector, $validator);

        // Save it to the user's browser in a cookie.
        helper('cookie');

        $appConfig = config('App');

        // Create the cookie
        set_cookie(
            'remember_f',                                  // Cookie Name
            $selector . ':' . $validator,                     // Value
            $this->config->rememberLength,              // # Seconds until it expires
            $appConfig->cookieDomain,
            $appConfig->cookiePath,
            $appConfig->cookiePrefix,
            $appConfig->cookieHTTPOnly,                 // Only send over HTTPS?
            true                                          // Hide from Javascript?
        );
    }


    /**
     * Returns the User ID for the current logged in user.
     *
     * @return int|null
     */
    public function id()
    {
        return $this->customer->id ?? null;
    }


    /**
     * Returns the User instance for the current logged in user.
     *
     * @return \Adnduweb\Ci4_ecommerce\Entities\Customer|null
     */
    public function customer()
    {
        return $this->customer;
    }

    /**
     * Grabs the current user from the database.
     *
     * @param array $wheres
     *
     * @return array|null|object
     */
    public function retrieveCustomer(array $wheres)
    {
        if (!$this->customerModel instanceof UuidModel) {
            throw AuthException::forInvalidModel('Customer');
        }

        $customer = $this->customerModel
            ->where($wheres)
            ->first();

        return $customer;
    }


    //--------------------------------------------------------------------
    // Model Setters
    //--------------------------------------------------------------------

    /**
     * Sets the model that should be used to work with
     * user accounts.
     *
     * @param \CodeIgniter\Model $model
     *
     * @return $this
     */
    public function setCustomerModel(UuidModel $model)
    {
        $this->customerModel = $model;

        return $this;
    }

    /**
     * Sets the model that should be used to record
     * login attempts (but failed and successful).
     *
     * @param Model $model
     *
     * @return $this
     */
    public function setLoginModel(Model $model)
    {
        $this->loginModel = $model;

        return $this;
    }
}

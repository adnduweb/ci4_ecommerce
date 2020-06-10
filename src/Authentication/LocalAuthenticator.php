<?php

namespace Adnduweb\Ci4_ecommerce\Authentication;

use CodeIgniter\Router\Exceptions\RedirectException;
use \Config\Services;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Exceptions\AuthException;

class LocalAuthenticator extends AuthenticationBase implements AuthenticatorInterface
{
    /**
     * Attempts to validate the credentials and log a customer in.
     *
     * @param array $credentials
     * @param bool  $remember Should we remember the customer (if enabled)
     *
     * @return bool
     */
    public function attempt(array $credentials, bool $remember = null): bool
    {
        $this->customer = $this->validate($credentials, true);

        if (empty($this->customer)) {
            // Always record a login attempt, whether success or not.
            $ipAddress = Services::request()->getIPAddress();
            $this->recordLoginAttempt($credentials['email'] ?? $credentials['username'], $ipAddress, $this->customer->id ?? null, false);

            $this->customer = null;
            return false;
        }

        if ($this->customer->isBanned()) {
            // Always record a login attempt, whether success or not.
            $ipAddress = Services::request()->getIPAddress();
            $this->recordLoginAttempt($credentials['email'] ?? $credentials['username'], $ipAddress, $this->customer->id ?? null, false);

            $this->error = lang('Authcustomer.userIsBanned');

            $this->customer = null;
            return false;
        }

        if (!$this->customer->isActivated()) {
            // Always record a login attempt, whether success or not.
            $ipAddress = Services::request()->getIPAddress();
            $this->recordLoginAttempt($credentials['email'] ?? $credentials['username'], $ipAddress, $this->customer->id ?? null, false);

            $param = http_build_query([
                'login' => urlencode($credentials['email'] ?? $credentials['username'])
            ]);

            $this->error = lang('Authcustomer.notActivated') . ' ' . anchor(route_to('resend-activate-account') . '?' . $param, lang('Authcustomer.activationResend'));

            $this->customer = null;
            return false;
        }

        return $this->login($this->customer, $remember);
    }

    /**
     * Checks to see if the user is logged in or not.
     *
     * @return bool
     */
    public function check(): bool
    {
        if ($this->isLoggedIn()) {
            // Do we need to force the user to reset their password?
            if ($this->customer && $this->customer->force_pass_reset) {
                throw new RedirectException(route_to('reset-password') . '?token=' . $this->customer->reset_hash);
            }

            return true;
        }

        // Check the remember me functionality.
        helper('cookie');
        $remember = get_cookie('remember_f');

        if (empty($remember)) {
            return false;
        }

        [$selector, $validator] = explode(':', $remember);
        $validator = hash('sha256', $validator);

        $token = $this->loginModel->getRememberToken($selector);

        if (empty($token)) {
            return false;
        }

        if (!hash_equals($token->hashedValidator, $validator)) {
            return false;
        }

        // Yay! We were remembered!
        $customer = $this->customerModel->find($token->customer_id);

        if (empty($customer)) {
            return false;
        }

        $this->login($customer);

        // We only want our remember me tokens to be valid
        // for a single use.
        $this->refreshRemember($customer->id, $selector);

        return true;
    }

    /**
     * Checks the user's credentials to see if they could authenticate.
     * Unlike `attempt()`, will not log the user into the system.
     *
     * @param array $credentials
     * @param bool  $returnCustomer
     *
     * @return bool|User
     */
    public function validate(array $credentials, bool $returnCustomer = false)
    {
        // Can't validate without a password.
        if (empty($credentials['password']) || count($credentials) < 2) {
            return false;
        }

        // Only allowed 1 additional credential other than password
        $password = $credentials['password'];
        unset($credentials['password']);

        if (count($credentials) > 1) {
            throw AuthException::forTooManyCredentials();
        }

        // Ensure that the fields are allowed validation fields
        if (!in_array(key($credentials), $this->config->validFields)) {
            throw AuthException::forInvalidFields(key($credentials));
        }

        // Can we find a user with those credentials?
        $customer = $this->customerModel->where($credentials)
            ->first();

        if (!$customer) {
            $this->error = lang('Authcustomer.badAttempt');
            return false;
        }

        // Now, try matching the passwords.
        $result = password_verify(base64_encode(
            hash('sha384', $password, true)
        ), $customer->password_hash);

        if (!$result) {
            $this->error = lang('Authcustomer.invalidPassword');
            return false;
        }

        // Check to see if the password needs to be rehashed.
        // This would be due to the hash algorithm or hash
        // cost changing since the last time that a user
        // logged in.
        if (password_needs_rehash($customer->password_hash, $this->config->hashAlgorithm)) {
            $customer->password = $password;
            $this->customerModel->save($customer);
        }

        return $returnCustomer
            ? $customer
            : true;
    }
}

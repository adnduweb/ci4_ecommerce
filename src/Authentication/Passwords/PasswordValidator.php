<?php

namespace Adnduweb\Ci4_ecommerce\Authentication\Passwords;

use Adnduweb\Ci4_ecommerce\Config\AuthCustomer;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Exceptions\AuthException;

class PasswordValidator
{
    /**
     * @var Auth
     */
    protected $config;

    protected $error;

    protected $suggestion;

    public function __construct(AuthCustomer $config)
    {
        $this->config = $config;
    }

    /**
     * Checks a password against all of the Validators specified
     * in `$passwordValidators` setting in Config\Auth.php.
     *
     * @param string $password
     * @param User   $user
     *
     * @return bool
     */
    public function check(string $password, Customer $user = null): bool
    {
        if (is_null($user)) {
            throw AuthException::forNoEntityProvided();
        }

        $password = trim($password);

        if (empty($password)) {
            $this->error = lang('Authcustomer.errorPasswordEmpty');

            return false;
        }

        $valid = false;

        foreach ($this->config->passwordValidators as $className) {
            $class = new $className();
            $class->setConfig($this->config);

            if ($class->check($password, $user) === false) {
                $this->error = $class->error();
                $this->suggestion = $class->suggestion();

                $valid = false;
                break;
            }

            $valid = true;
        }

        return $valid;
    }

    /**
     * Returns the current error, as defined by validator
     * it failed to pass.
     *
     * @return mixed
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Returns a string with any suggested fix
     * based on the validator it failed to pass.
     *
     * @return mixed
     */
    public function suggestion()
    {
        return $this->suggestion;
    }
}

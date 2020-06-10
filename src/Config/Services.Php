<?php

namespace Adnduweb\Ci4_ecommerce\Config;


use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;

use CodeIgniter\Model;
use Michalsn\Uuid\UuidModel;
use Adnduweb\Ci4_ecommerce\Authorization\FlatAuthorization;
use Adnduweb\Ci4_ecommerce\Models\CustomerModel;
use Adnduweb\Ci4_ecommerce\Models\LoginModel;
use Adnduweb\Ci4_ecommerce\Authorization\GroupModel;
use Adnduweb\Ci4_ecommerce\Authentication\Passwords\PasswordValidator;
use Adnduweb\Ci4_ecommerce\Authentication\Activators\CustomerActivator;
use Adnduweb\Ci4_ecommerce\Authentication\Resetters\CustomerResetter;

class Services extends CoreServices
{
    public static function cart($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('cart');
        }

        // If no config was injected then load one
        // Prioritizes app/Config if found
        if (empty($config))
            $config = config('Visits');

        return new \Adnduweb\Ci4_ecommerce\Cart();
    }

    public static function authenticationCustomer(string $lib = 'local', UuidModel $customerModel = null, Model $loginModel = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('authenticationCustomer', $lib, $customerModel, $loginModel);
        }

        // config() checks first in app/Config
        $config = config('AuthCustomer');

        $class = $config->authenticationCustomerLibs[$lib];

        $instance = new $class($config);

        if (empty($customerModel)) {
            $customerModel = new CustomerModel();
        }

        if (empty($loginModel)) {
            $loginModel = new LoginModel();
        }

        return $instance
            ->setCustomerModel($customerModel)
            ->setLoginModel($loginModel);
    }

    public static function authorizationCustomer(Model $groupModel = null, Model $permissionModel = null, UuidModel $customerModel = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('authorizationCustomer', $groupModel, $permissionModel, $customerModel);
        }

        if (is_null($groupModel)) {
            $groupModel = new GroupModel();
        }

        if (is_null($permissionModel)) {
            $permissionModel = new PermissionModel();
        }

        $instance = new FlatAuthorization($groupModel, $permissionModel);

        if (is_null($customerModel)) {
            $customerModel = new CustomerModel();
        }

        return $instance->setcustomerModel($customerModel);
    }

    /**
     * Returns an instance of the password validator.
     *
     * @param null $config
     * @param bool $getShared
     *
     * @return mixed|PasswordValidator
     */
    public static function passwordsCustomer($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('passwordsCustomer', $config);
        }

        if (empty($config)) {
            $config = config(AuthCustomer::class);
        }

        return new PasswordValidator($config);
    }

    /**
     * Returns an instance of the activator.
     *
     * @param null $config
     * @param bool $getShared
     *
     * @return mixed|Activator
     */
    public static function activatorCustomer($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('activatorCustomer', $config);
        }

        if (empty($config)) {
            $config = config(AuthCustomer::class);
        }

        return new CustomerActivator($config);
    }

    /**
     * Returns an instance of the resetter.
     *
     * @param null $config
     * @param bool $getShared
     *
     * @return mixed|Activator
     */
    public static function resetterCustomer($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('resetterCustomer', $config);
        }

        if (empty($config)) {
            $config = config(AuthCustomer::class);
        }

        return new CustomerResetter($config);
    }
}

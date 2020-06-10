<?php

namespace Adnduweb\Ci4_ecommerce\Authentication\Activators;

use Adnduweb\Ci4_ecommerce\Config\Authcustomer;
use Adnduweb\Ci4_ecommerce\Entities\customer;

class customerActivator
{
    /**
     * @var Authcustomer
     */
    protected $config;

    protected $error;

    public function __construct(Authcustomer $config)
    {
        $this->config = $config;
    }

    /**
     * Sends activation message to the customer via specified class
     * in `$requireActivation` setting in Config\Authcustomer.php.
     *
     * @param Customer $customer
     *
     * @return bool
     */
    public function send(Customer $customer = null, string $template): bool
    {
        if ($this->config->requireActivation === false) {
            return true;
        }

        $className = $this->config->requireActivation;

        $class = new $className();
        $class->setConfig($this->config);

        if ($class->send($customer, $template) === false) {
            log_message('error', "Failed to send activation messaage to: {$customer->email}");
            $this->error = $class->error();

            return false;
        }

        return true;
    }

    /**
     * Returns the current error.
     *
     * @return mixed
     */
    public function error()
    {
        return $this->error;
    }
}

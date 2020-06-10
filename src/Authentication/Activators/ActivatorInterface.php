<?php

namespace Adnduweb\Ci4_ecommerce\Authentication\Activators;

use CodeIgniter\Entity;

/**
 * Interface ActivatorInterface
 *
 * @package Adnduweb\Ci4_ecommerce\Authentication\Activators
 */
interface ActivatorInterface
{
    /**
     * Send activation message to user
     *
     * @param Customer $customer
     *
     * @return mixed
     */
    public function send(Entity $customer = null, string $template): bool;

    /**
     * Returns the error string that should be displayed to the user.
     *
     * @return string
     */
    public function error(): string;
}

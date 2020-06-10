<?php

namespace Adnduweb\Ci4_ecommerce\Authentication\Resetters;

use CodeIgniter\Entity;

/**
 * Interface ResetterInterface
 *
 * @package Adnduweb\Ci4_ecommerce\Authentication\Resetters
 */
interface ResetterInterface
{
    /**
     * Send reset message to user
     *
     * @param User $user
     *
     * @return mixed
     */
    public function send(Entity $user = null): bool;

    /**
     * Returns the error string that should be displayed to the user.
     *
     * @return string
     */
    public function error(): string;
}

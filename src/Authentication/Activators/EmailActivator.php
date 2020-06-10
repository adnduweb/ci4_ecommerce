<?php

namespace Adnduweb\Ci4_ecommerce\Authentication\Activators;

use Config\Email;
use CodeIgniter\Entity;
use CodeIgniter\Config\Services;

/**
 * Class EmailActivator
 *
 * Sends an activation email to user.
 *
 * @package Adnduweb\Ci4_ecommerce\Authentication\Activators
 */
class EmailActivator extends BaseActivator implements ActivatorInterface
{
    /**
     * @var string
     */
    protected $error;

    /**
     * Sends an activation email
     *
     * @param Customer $customer
     *
     * @return mixed
     */
    public function send(Entity $customer = null, string $template): bool
    {
        $email = Services::email();
        $config = new Email();

        $settings = $this->getActivatorSettings();

        $sent = $email->setFrom($settings->fromEmail ?? $config->fromEmail, $settings->fromName ?? $config->fromName)
            ->setTo($customer->email)
            ->setSubject(lang('Authcustomer.activationSubject'))
            ->setMessage(view($template, ['hash' => $customer->activate_hash]))
            ->setMailType('html')
            ->send();

        if (!$sent) {
            $this->error = lang('Authcustomer.errorSendingActivation', [$customer->email]);
            return false;
        }

        return true;
    }

    /**
     * Returns the error string that should be displayed to the user.
     *
     * @return string
     */
    public function error(): string
    {
        return $this->error ?? '';
    }
}

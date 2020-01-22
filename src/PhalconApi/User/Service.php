<?php

namespace PhalconApi\User;

use PhalconApi\Constants\ErrorCodes;
use PhalconApi\Exception;
use PhalconApi\Mvc\Plugin;

class Service extends Plugin
{

    private function getSession() {
        $session = $this->authManager->getSession();

        // FIX: 2020-01-22 00:23 Fix that session (token) is not retrieved right
        if (is_null($session)) {
            try {
                $token = $this->request->getToken();
                $session = $this->tokenParser->getSession($token);
            } catch (\Exception $e) {
                throw new Exception(ErrorCodes::AUTH_TOKEN_INVALID);
            }
        }
    }

    /**
     * Returns details for the current user, e.g. a User model
     *
     * @return mixed
     * @throws Exception
     */
    public function getDetails()
    {
        $details = null;

        $session = $this->getSession();

        if ($session) {
            $identity = $session->getIdentity();
            $details = $this->getDetailsForIdentity($identity);
        }

        return $details;
    }

    /**
     * This method should return details for the provided identity. Override this method with your own implementation.
     *
     * @param mixed $identity Identity to get the details from
     *
     * @return mixed The details for the provided identity
     * @throws Exception
     */
    protected function getDetailsForIdentity($identity)
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_IMPLEMENTED, null,
            'Unable to get details for identity, method getDetailsForIdentity in user service not implemented. ' .
            'Make a subclass of \PhalconApi\User\Service with an implementation for this method, and register it in your DI.');
    }

    /**
     * Returns the identity for the current user, e.g. the user ID
     *
     * @return mixed
     */
    public function getIdentity()
    {
        $session = $this->getSession();

        if ($session) {
            return $session->getIdentity();
        }

        return null;
    }

    /**
     * This method should return the role for the current user
     *
     * @return string Name of the role for the current user
     * @throws Exception
     */
    public function getRole()
    {
        throw new Exception(ErrorCodes::GENERAL_NOT_IMPLEMENTED, null,
            'Unable to get role for identity, method getRole in user service not implemented. ' .
            'Make a subclass of \PhalconApi\User\Service with an implementation for this method, and register it in your DI.');
    }
}


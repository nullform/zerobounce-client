<?php

namespace Nullform\ZeroBounce;

use Nullform\ZeroBounce\Exceptions\AbstractException;
use Nullform\ZeroBounce\Models\Email;

/**
 * Facade.
 *
 * @package Nullform\ZeroBounce
 */
class Facade
{
    /**
     * @var Client[]
     */
    protected static $clients;


    /**
     * Validate email.
     *
     * @param string      $api_key
     * @param string      $email
     * @param string|null $ip_address
     * @return Email
     * @throws \Exception
     * @uses Client::validate()
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-validate-emails/ Validation API: Validate Emails
     */
    public static function validate(string $api_key, string $email, ?string $ip_address = null): Email
    {
        try {
            $result = static::getClient($api_key)->validate($email, (string)$ip_address);
        } catch (AbstractException $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $result;
    }

    /**
     * The amount of credits you have left in your account for email validations.
     * If a -1 is returned, that means your API Key is invalid.
     *
     * @param string $api_key
     * @return int
     * @throws \Exception
     * @uses Client::getCredits()
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-credit-balance/ Validation API: Credit Balance
     */
    public static function getCredits(string $api_key): int
    {
        $credits = -1;

        try {
            $credits = static::getClient($api_key)->getCredits();
        } catch (AbstractException $exception) {
            throw new \Exception($exception->getMessage());
        }

        return (int)$credits;
    }

    /**
     * Get client instance.
     *
     * @param string $api_key
     * @return Client
     */
    protected static function getClient(string $api_key): Client
    {
        if (empty(static::$clients[$api_key])) {
            static::$clients[$api_key] = new Client($api_key);
        }
        return static::$clients[$api_key];
    }
}
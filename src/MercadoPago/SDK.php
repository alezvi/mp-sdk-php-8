<?php

namespace MercadoPago;

use Exception;

/**
 * MercadoPagoSdk Class Doc Comment
 *
 * @package MercadoPago
 */
class SDK
{
    /**
     * @var Config
     */
    protected static Config $_config;

    /**
     * @var Manager
     */
    protected static Manager $_manager;

    /**
     * @var RestClient
     */
    protected static RestClient $_restClient;

    /**
     * MercadoPagoSdk constructor.
     * @throws Exception
     */
    public static function initialize(): void
    {
        self::$_restClient = new RestClient();
        self::$_config = new Config(null, self::$_restClient);
        self::$_restClient->setHttpParam('address', self::$_config->get('base_url'));
        self::$_manager = new Manager(self::$_restClient, self::$_config);
        Entity::setManager(self::$_manager);
    }

    /**
     * Set Access Token for SDK .
     * @throws Exception
     */
    public static function setAccessToken($access_token): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }

        self::$_config->configure(['ACCESS_TOKEN' => $access_token]);

    }

    /**
     * @return mixed|null
     */
    public static function getAccessToken(): mixed
    {
        return self::$_config->get('ACCESS_TOKEN');
    }

    /**
     * @return mixed|null
     */
    public static function getCountryId(): mixed
    {
        return self::$_config->get('COUNTRY_ID');
    }

    /**
     * @return void
     */
    public static function cleanCredentials(): void
    {
        if (self::$_config) {
            self::$_config->clean();
        }
    }

    /**
     * @param $array
     * @return void
     */
    public static function setMultipleCredentials($array): void
    {
        foreach ($array as $key => $values) {
            self::$_config->configure([$key => $values]);
        }
    }

    /**
     * Set Access ClientId for SDK .
     * @throws Exception
     */
    public static function setClientId($client_id): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }

        self::$_config->configure(['CLIENT_ID' => $client_id]);
    }

    /**
     * @return mixed|null
     */
    public static function getClientId(): mixed
    {
        return self::$_config->get('CLIENT_ID');
    }

    /**
     * Set Access ClientSecret for SDK .
     * @throws Exception
     */
    public static function setClientSecret($client_secret): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }

        self::$_config->configure(['CLIENT_SECRET' => $client_secret]);
    }

    /**
     * @return mixed|null
     */
    public static function getClientSecret(): mixed
    {
        return self::$_config->get('CLIENT_SECRET');
    }

    /**
     * Set Access ClientSecret for SDK .
     */
    public static function setPublicKey($public_key): void
    {
        self::$_config->configure(['PUBLIC_KEY' => $public_key]);
    }

    /**
     * @return mixed|null
     */
    public static function getPublicKey(): mixed
    {
        return self::$_config->get('PUBLIC_KEY');
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public static function configure(array $data = []): void
    {
        self::initialize();
        self::$_config->configure($data);
    }

    /**
     * @return Config
     */
    public static function config(): Config
    {
        return self::$_config;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public static function addCustomTrackingParam($key, $value): void
    {
        self::$_manager->addCustomTrackingParam($key, $value);
    }

    // Publishing generic functions 

    /**
     * @throws Exception
     */
    public static function get($uri, $options = []): array
    {
        return self::$_restClient->get($uri, $options);
    }

    /**
     * @throws Exception
     */
    public static function post($uri, $options = []): array
    {
        return self::$_restClient->post($uri, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return array
     * @throws Exception
     */
    public static function put($uri, array $options = []): array
    {
        return self::$_restClient->put($uri, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    public static function delete($uri, array $options = []): mixed
    {
        return self::$_restClient->delete($uri, $options);
    }

    /**
     * Set Platform Id for SDK .
     * @throws Exception
     */
    public static function setPlatformId($platform_id): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }
        self::$_config->configure(['x-platform-id' => $platform_id]);
        self::addCustomTrackingParam('x-platform-id', $platform_id);
    }

    /**
     * @return mixed|null
     */
    public static function getPlatformId(): mixed
    {
        return self::$_config->get('x-platform-id');
    }

    /**
     * Set Corporation Id for SDK .
     * @throws Exception
     */
    public static function setCorporationId($corporation_id): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }
        self::$_config->configure(['x-corporation-id' => $corporation_id]);
        self::addCustomTrackingParam('x-corporation-id', $corporation_id);
    }

    /**
     * @return mixed|null
     */
    public static function getCorporationId(): mixed
    {
        return self::$_config->get('x-corporation-id');
    }

    /**
     * Set Integrator Id for SDK .
     * @throws Exception
     */
    public static function setIntegratorId($integrator_id): void
    {
        if (!isset(self::$_config)) {
            self::initialize();
        }
        self::$_config->configure(['x-integrator-id' => $integrator_id]);
        self::addCustomTrackingParam('x-integrator-id', $integrator_id);
    }

    /**
     * @return mixed|null
     */
    public static function getIntegratorId(): mixed
    {
        return self::$_config->get('x-integrator-id');
    }
}

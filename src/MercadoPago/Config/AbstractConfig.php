<?php

namespace MercadoPago\Config;

/**
 * AbstractConfig Class Doc Comment
 *
 * @package MercadoPago\Config
 */
abstract class AbstractConfig
{
    /**
     * @var array|null
     */
    protected ?array $data = null;

    /**
     * @var array
     */
    protected array $cache = [];

    /**
     * AbstractConfig constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = array_merge($this->getDefaults(), $data);
    }

    /**
     * @return array
     */
    protected function getDefaults(): array
    {
        return [];
    }

    public function clean(): array
    {
        return $this->data = array(
            'base_url' => 'https://api.mercadopago.com',
        );
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null): mixed
    {

        if ($this->has($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return (array_key_exists($key, $this->data));
    }

    /**
     * @return array|null
     */
    public function all(): ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function configure(array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

}
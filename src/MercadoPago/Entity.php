<?php

namespace MercadoPago;

use MercadoPago\Annotation\Attribute;
use Exception;

/**
 * Class Entity
 *
 * @package MercadoPago
 */
abstract class Entity
{
    /**
     * @var
     */
    protected static $_custom_headers = array();

    /**
     * @var
     */
    protected static $_manager;

    /**
     * @Attribute(serialize = false)
     */
    protected $_last;

    /**
     * @var
     */
    protected $error;

    /**
     * @todo remove if unused
     * @var
     */
    protected $_pagination_params;

    /**
     * @Attribute(serialize = false)
     */
    protected $_empty = false;

    /**
     * Entity constructor.
     *
     * @param array $params
     *
     * @throws Exception
     */
    public function __construct(array $params = [])
    {
        if (empty(self::$_manager)) {
            throw new Exception('Please initialize SDK first');
        }

        self::$_manager->setEntityMetaData($this);

        $this->_fillFromArray($this, $params);
    }

    /**
     * @return mixed
     */
    public function Error()
    {
        return $this->error;
    }

    /**
     * @param Manager $manager
     */
    public static function setManager(Manager $manager): void
    {
        self::$_manager = $manager;
    }

    /**
     * @return void
     */
    public static function unSetManager(): void
    {
        self::$_manager = null;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function get($id)
    {
        return self::read(["id" => $id]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function find_by_id($id)
    {
        return self::read(["id" => $id]);
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public static function setCustomHeader($key, $value): void
    {
        self::$_custom_headers[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getCustomHeader($key)
    {
        return self::$_custom_headers[$key];
    }

    /**
     * @param $array
     * @return void
     */
    public static function setCustomHeadersFromArray(array $array = []): void
    {
        foreach ($array as $key => $value) {
            self::setCustomHeader($key, $value);
        }
    }

    /**
     * @return array
     */
    public static function getCustomHeaders(): array
    {
        return self::$_custom_headers;
    }

    /**
     * @return mixed
     */
    public function not_found(): mixed
    {
        return $this->_empty;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    public static function read(array $params = [], array $options = []): mixed
    {
        $class = get_called_class();

        $entity = new $class();

        self::$_manager->setEntityUrl($entity, 'read', $params);

        self::$_manager->cleanEntityDeltaQueryJsonData($entity);

        $response = self::$_manager->execute($entity, 'get', $options);

        if ($response['code'] == "200" || $response['code'] == "201") {
            $entity->_fillFromArray($entity, $response['body']);
            $entity->_last = clone $entity;
            return $entity;
        } elseif (intval($response['code']) == 404) {
            return null;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            throw new Exception ($response['body']['message']);
        } else {
            throw new Exception ("Internal API Error");
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function all($options = []): mixed
    {
        $params = [];
        $class = get_called_class();
        $entity = new $class();
        $entities = array();

        self::$_manager->setEntityUrl($entity, 'list', $params);
        self::$_manager->cleanQueryParams($entity);
        $response = self::$_manager->execute($entity, 'get');

        if ($response['code'] == "200" || $response['code'] == "201") {
            $results = $response['body'];
            foreach ($results as $result) {
                $entity = new $class();
                $entity->_fillFromArray($entity, $result);
                $entities[] = $entity;
            }
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            throw new Exception ($response['error'] . " " . $response['message']);
        } else {
            throw new Exception ("Internal API Error");
        }

        return $entities;
    }

    /**
     * @param array $filters
     * @param array $options
     * @return mixed
     * @throws Exception
     */
    public static function search(array $filters = [], array $options = []): mixed
    {
        $class = get_called_class();
        $searchResult = new SearchResultsArray();
        $searchResult->setEntityTypes($class);
        $entityToQuery = new $class();

        self::$_manager->setEntityUrl($entityToQuery, 'search');
        self::$_manager->cleanQueryParams($entityToQuery);
        self::$_manager->setQueryParams($entityToQuery, $filters);

        $response = self::$_manager->execute($entityToQuery, 'get');
        if ($response['code'] == "200" || $response['code'] == "201") {
            $searchResult->fetch($filters, $response['body']);
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            $searchResult->process_error_body($response['body']);
            throw new Exception($response['body']['message']);
        } else {
            throw new Exception("Internal API Error");
        }

        return $searchResult;
    }

    /**
     * @codeCoverageIgnore
     * @return mixed
     */
    public function APCIteratorAll(): mixed
    {
        self::$_manager->setEntityUrl($this, 'list');
        return self::$_manager->execute($this, 'get');
    }

    /**
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function update(array $options = []): bool
    {
        $params = [];
        self::$_manager->setEntityUrl($this, 'update', $params);
        self::$_manager->setEntityDeltaQueryJsonData($this);

        $response = self::$_manager->execute($this, 'put');

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
            return true;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            // A recuperable error 
            $this->process_error_body($response['body']);
            return false;
        } else {
            throw new Exception ("Internal API Error");
        }
    }

    /**
     * @codeCoverageIgnore
     * @return void
     */
    public static function destroy(): void
    {
        //return self::$_manager->execute(get_called_class(), '');
    }

    /**
     * @param $method
     * @param $action
     * @return mixed
     */
    public function custom_action($method, $action): mixed
    {
        self::$_manager->setEntityUrl($this, $action);
        self::$_manager->setEntityQueryJsonData($this);

        $response = self::$_manager->execute($this, $method);

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
        }

        return $response;
    }

    /**
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function save($options = []): bool
    {
        self::$_manager->setEntityUrl($this, 'create');
        self::$_manager->setEntityQueryJsonData($this);

        $response = self::$_manager->execute($this, 'post', $options);

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
            $this->_last = clone $this;
            return true;
        } elseif (intval($response['code']) >= 300 && intval($response['code']) < 500) {
            // A recuperable error
            $this->process_error_body($response['body']);
            return false;
        } else {
            // Trigger an exception
            throw new Exception ("Internal API Error");
        }
    }

    /**
     * @param $message
     * @return void
     */
    function process_error_body($message)
    {
        $recuperable_error = new RecuperableError(
            $message['message'],
            ($message['error'] ?? ''),
            $message['status']
        );

        if (isset($message['cause'])) {
            $recuperable_error->proccess_causes($message['cause']);
        }

        $this->error = $recuperable_error;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->{$name};
    }


    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->{$name});
    }

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $this->_setValue($name, $value);
        return $this->{$name};
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param null $attributes
     *
     * @return array
     */
    public function toArray($attributes = null): array
    {
        $result = null;

        $excluded_attributes = self::$_manager->getExcludedAttributes($this);

        if (is_null($attributes)) {
            $result = get_object_vars($this);
        } else {
            $result = array_intersect_key(get_object_vars($this), $attributes);
        }

        foreach ($excluded_attributes as $excluded_attribute) {
            unset($result[$excluded_attribute]);
        }

        foreach ($result as $key => $value) {
            if (!is_bool($value) && empty($value)) {
                unset($result[$key]);
            }
        }

        return $result;

    }

    /**
     * @param $property
     * @param $value
     *
     * @throws Exception
     */
    protected function _setValue($property, $value, $validate = true): void
    {
        if ($this->_propertyExists($property)) {
            if ($validate) {
                self::$_manager->validateAttribute($this, $property, ['maxLength', 'readOnly'], $value);
            }
            if ($this->_propertyTypeAllowed($property, $value)) {
                $this->{$property} = $value;
            } else {
                $this->{$property} = $this->tryFormat($value, $this->_getPropertyType($property), $property);
            }
        } else {
            if ($this->_getDynamicAttributeDenied()) {
                throw new Exception('Dynamic attribute: ' . $property . ' not allowed for entity ' . get_class($this));
            }
            $this->{$property} = $value;
        }
    }

    /**
     * @param $property
     *
     * @return bool
     */
    protected function _propertyExists($property): bool
    {
        return array_key_exists($property, get_object_vars($this));
    }

    /**
     * @param $property
     * @param $type
     *
     * @return bool
     */
    protected function _propertyTypeAllowed($property, $type): bool
    {
        $definedType = $this->_getPropertyType($property);
        if (!$definedType) {
            return true;
        }
        if (is_object($type) && class_exists($definedType, false)) {
            return ($type instanceof $definedType);
        }
        return gettype($type) == $definedType;
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    protected function _getPropertyType($property): mixed
    {
        return self::$_manager->getPropertyType(get_called_class(), $property);
    }

    /**
     * @return mixed
     */
    protected function _getDynamicAttributeDenied(): mixed
    {
        return self::$_manager->getDynamicAttributeDenied(get_called_class());
    }

    /**
     * @param $value
     * @param $type
     * @param $property
     *
     * @return array|bool|float|int|string
     * @throws Exception
     */
    protected function tryFormat($value, $type, $property): float|int|bool|array|string
    {
        try {
            switch ($type) {
                case 'float':
                    if (!is_numeric($value)) {
                        break;
                    }
                    return (float)$value;
                case 'int':
                    if (!is_numeric($value)) {
                        break;
                    }
                    return (int)$value;
                case 'string':
                    return (string)$value;
                case 'array':
                    return (array)$value;
                case 'date':
                    if (empty($value)) {
                        return $value;
                    };
                    if (is_string($value)) {
                        return date("Y-m-d\TH:i:s.000P", strtotime($value));
                    } else {
                        return $value->format('Y-m-d\TH:i:s.000P');
                    }

            }
        } catch (Exception $e) {
            throw new Exception('Wrong type ' . gettype($value) . '. Cannot convert ' . $type . ' for property ' . $property);
        }
        throw new Exception('Wrong type ' . gettype($value) . '. It should be ' . $type . ' for property ' . $property);
    }

    /**
     * Fill entity from data with nested object creation
     *
     * @param $entity
     * @param $data
     */
    public function fillFromArray($entity, $data): void
    {
        $this->_fillFromArray($entity, $data);
    }

    /**
     * Fill entity from data with nested object creation
     *
     * @param $entity
     * @param $data
     */
    protected function _fillFromArray($entity, $data): void
    {
        if ($data) {
            foreach ($data as $key => $value) {
                if (!is_null($value)) {
                    if (is_array($value)) {
                        $className = 'MercadoPago\\' . $this->_camelize($key);
                        if (class_exists($className, true)) {
                            $entity->_setValue($key, new $className, false);
                            $entity->_fillFromArray($this->{$key}, $value);
                        } else {
                            $entity->_setValue($key, json_decode(json_encode($value)), false);
                        }
                        continue;
                    }
                    $entity->_setValue($key, $value, false);
                }
            }
        }
    }

    /**
     * @param        $input
     * @param string $separator
     * @return string
     */
    protected function _camelize($input, string $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * @param array $options
     * @return bool
     * @throws Exception
     */
    public function delete(array $options = []): bool
    {
        $params = [];
        self::$_manager->setEntityUrl($this, 'delete', $params);

        $response = self::$_manager->execute($this, 'delete');

        if ($response['code'] == "200" || $response['code'] == "201") {
            $this->_fillFromArray($this, $response['body']);
            return true;
        } elseif (intval($response['code']) >= 400 && intval($response['code']) < 500) {
            if (!is_null($response['body'])) {
                $this->process_error_body($response['body']);
            }
            return false;
        } else {
            throw new Exception ("Internal API Error");
        }
    }
}


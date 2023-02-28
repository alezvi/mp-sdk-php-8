<?php

namespace MercadoPago;

use ArrayObject;
use Exception;

class SearchResultsArray extends ArrayObject
{
    /**
     * @var array
     */
    public array $_filters;

    /**
     * @var int
     */
    public int $limit;

    /**
     * @var int
     */
    public int $total;

    /**
     * @var int
     */
    public int $offset;

    /**
     * @var RecuperableError
     */
    public RecuperableError $errors;

    /**
     * @var Entity
     */
    public Entity $_class;

    /**
     * @param $class
     * @return void
     */
    public function setEntityTypes($class): void
    {
        $this->_class = $class;
    }

    /**
     * @param $params
     * @return void
     */
    public function setPaginateParams($params)
    {
        $this->limit = $params["limit"];
        $this->total = $params["total"];
        $this->offset = $params["offset"];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function next()
    {
        $new_offset = $this->limit + $this->offset;
        $this->_filters['offset'] = $new_offset;
        $result = $this->_class::search($this->_filters);

        $this->limit = $result->limit;
        $this->offset = $result->offset;
        $this->total = $result->total;

        $this->exchangeArray($result->getArrayCopy());
    }

    /**
     * @param $filters
     * @param $body
     * @return void
     */
    public function fetch($filters, $body)
    {
        $this->_filters = $filters;

        if ($body) {
            $results = [];
            if (array_key_exists("results", $body)) {
                $results = $body["results"];
            } else if (array_key_exists("elements", $body)) {
                $results = $body["elements"];
            }

            foreach ($results as $result) {
                $entity = new $this->_class();
                $entity->fillFromArray($entity, $result);
                $this->append($entity);
            }

            $this->fetchPaging($filters, $body);
        }
    }

    /**
     * @param $message
     * @return void
     */
    public function process_error_body($message)
    {
        $recuperable_error = new RecuperableError(
            $message['message'],
            $message['error'],
            $message['status']
        );

        foreach ($message['cause'] as $causes) {
            if (is_array($causes)) {
                foreach ($causes as $cause) {
                    $recuperable_error->add_cause($cause['code'], $cause['description']);
                }
            } else {
                $recuperable_error->add_cause($cause['code'], $cause['description']);
            }
        }

        $this->errors = $recuperable_error;
    }

    /**
     * @param $filters
     * @param $body
     * @return void
     */
    private function fetchPaging($filters, $body)
    {
        if (array_key_exists("paging", $body)) {
            $paging = $body["paging"];
            $this->limit = $paging["limit"];
            $this->total = $paging["total"];
            $this->offset = $paging["offset"];
        } else {
            $this->offset = array_key_exists("offset", $filters) ? $filters["offset"] : 0;
            $this->limit = array_key_exists("limit", $filters) ? $filters["limit"] : 20;
            $this->total = array_key_exists("total", $body) ? $body["total"] : 0;
        }
    }
}
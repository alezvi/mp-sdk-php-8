<?php

namespace MercadoPago\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class RestMethod extends Annotation
{
    /**
     * The endpoint URI
     * @var string
     */
    public string $resource;

    /**
     * @var string
     */
    public string $method;

    /**
     * @var bool
     */
    public bool $idempotency;
}
<?php

namespace MercadoPago\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Attribute extends Annotation
{
    /**
     * @var string
     */
    public string $type;

    /**
     * @var bool
     */
    public bool $required = false;

    /**
     * @var bool
     */
    public bool $serialize = true ;

    /**
     * @var bool
     */
    public bool $readOnly;

    /**
     * @var bool
     */
    public bool $primaryKey;

    /**
     * @var bool
     */
    public bool $idempotency;

    /**
     * @var mixed
     */
    public mixed $defaultValue;

    /**
     * @var int
     */
    public int $maxLength;
}
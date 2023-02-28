<?php

namespace MercadoPago\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class DenyDynamicAttribute extends Annotation
{
    /**
     * @var mixed
     */
    public $value;
}
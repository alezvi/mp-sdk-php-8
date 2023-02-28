<?php

namespace MercadoPago\Annotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class RequestParam extends Annotation
{
    /**
     * @var mixed
     */
    public mixed $param;
}
<?php

namespace MercadoPago\Tests;

use MercadoPago\Entity;

/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class MercadoPagoSdkTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
    protected function setUp(): void
    {
        Entity::unSetManager();
    }

    /**
     *
     */
    protected function tearDown(): void
    {
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Please initialize SDK first
     */
    public function testWrongInitialization()
    {
        $entity = new DummyEntity();
    }

    /**
     * @throws \Exception
     */
    public function testInitialization()
    {
        SDK::initialize();
        $entity = new DummyEntity();
        $this->assertInstanceOf(DummyEntity::class, $entity);
    }
}
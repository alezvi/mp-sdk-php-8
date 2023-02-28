<?php

namespace MercadoPago\Tests;

use Exception;
use Dotenv\Dotenv;
use MercadoPago\SDK;
use PHPUnit\Framework\TestCase;

/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class SDKTest extends TestCase
{
    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        SDK::cleanCredentials();

        if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = new Dotenv(__DIR__, '/../.env');
            $dotenv->load();
        }

        SDK::setClientId(getenv('CLIENT_ID'));
        SDK::setClientSecret(getenv('CLIENT_SECRET'));
    }

    /**
     * @covers                   \MercadoPago\SDK
     */
    public function testSettings()
    {
        $this->assertEquals(getenv('CLIENT_ID'), SDK::getClientId());
        $this->assertEquals(getenv('CLIENT_SECRET'), SDK::getClientSecret());

    }

    /**
     * @covers                   SDK
     */
    public function testDoGetToken()
    {
        $this->assertNotNull(SDK::getAccessToken());
    }

    public function testSetMultipleAT()
    {
        SDK::setMultipleCredentials(
            array(
                "mla" => "MLA_AT",
                "mlb" => "MLB_AT"
            )
        );
        $this->assertNotNull(SDK::config()->getData()['mla']);
        $this->assertNotNull(SDK::config()->getData()['mlb']);
    }
}

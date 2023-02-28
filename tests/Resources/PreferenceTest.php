<?php

namespace MercadoPago\Tests\Resources;

use DateTime;
use Dotenv\Dotenv;
use MercadoPago\Item;
use MercadoPago\Preference;
use MercadoPago\SDK;
use PHPUnit\Framework\TestCase;

/**
 * EntityTest Class Doc Comment
 *
 * @package MercadoPago
 */
class PreferenceTest extends TestCase
{
    private static $last_preference;

    public static function setUpBeforeClass(): void
    {
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = new Dotenv(__DIR__, '../../.env');
            $dotenv->load();
        }

        SDK::setAccessToken(getenv('ACCESS_TOKEN'));
    }

    public function testCreatePreference()
    {
        $preference = new PreferenceTest();

        # Building an item
        $item = new Item();
        $item->title = "item";
        $item->quantity = 1;
        $item->unit_price = 100;

        $preference->items = array($item);
        $preference->expiration_date_to = new DateTime('tomorrow');
        $preference->save();

        self::$last_preference = $preference;

        $this->assertTrue($preference->sandbox_init_point != null);
    }

    public function testFindPreferenceById()
    {
        $preference = Preference::find_by_id(self::$last_preference->id);
        $this->assertEquals($preference->id, self::$last_preference->id);
    }
}

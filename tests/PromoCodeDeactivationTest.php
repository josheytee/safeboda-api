<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PromoCodeDeactivationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Test promo code deactivation endpoint
     *
     * @return void
     */
    public function testDeactivationSuccess()
    {
        $promoCode = factory(App\PromoCode::class)->create();

        $response = $this->put(route('promo-code-deactivate', ['code' => $promoCode->code]));
        $response->seeJsonStructure([
            'message',
        ]);
        $response->assertResponseStatus(200);
    }

    /**
     * Test promo code deactivation endpoint for non-existent promo code
     *
     * @return void
     */
    public function testPromoCodeNotFound()
    {
        $response = $this->put(route('promo-code-deactivate', ['code' => 200]));
        $response->seeJsonStructure([
            'error',
        ]);
        $response->assertResponseStatus(404);
    }
}

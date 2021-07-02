<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PromoCodeRadiusConfigurationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Test promo code radius configuration endpoint
     *
     * @return void
     */
    public function testRadiusConfigSuccess()
    {
        $promoCode = factory(App\PromoCode::class)->create();
        $response = $this->put(
            route('promo-code-radius-config', ['code' => $promoCode->code]),
            ['radius' => 500]
        );

        $response->seeJsonStructure(['promoCode' => ['id', 'code', 'radius', 'amount', 'created_at', 'expires_at'],
        ]);
        $response->assertResponseStatus(200);
    }

    /**
     * Test promo code radius configuration endpoint for validation errors
     *
     * @return void
     */
    public function testRadiusConfigValidationError()
    {
        $promoCode = factory(App\PromoCode::class)->create();
        $response = $this->put(route('promo-code-radius-config', ['code' => $promoCode->code]), []);
        $response->seeJsonStructure([
            'errors' => [],
        ]);
        $response->assertResponseStatus(422);
    }

    /**
     * Test promo code radius configuration endpoint for non-existent promo code
     *
     * @return void
     */
    public function testPromoCodeNotFound()
    {
        $response = $this->put(
            route('promo-code-radius-config', ['code' => "AAAAAA"]),
            ['radius' => 500]
        );
        $response->seeJsonStructure([
            'error',
        ]);
        $response->assertResponseStatus(404);
    }
}

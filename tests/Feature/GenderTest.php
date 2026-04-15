<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonImmutable;

class GenderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    private function fakeGenderizeResponse(array $payload, int $status = 200): void
    {
        Http::fake([
            'https://api.genderize.io*' => Http::response($payload, $status),
        ]);
    }

    private function assertIso8601Utc(string $value): void
    {
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z$/',
            $value
        );

        $this->assertEquals(0, CarbonImmutable::parse($value)->getOffset());
    }

    public function test_successful_confident_result()
    {
        $this->fakeGenderizeResponse([
            'name' => 'john',
            'gender' => 'male',
            'probability' => 0.99,
            'count' => 1234,
        ]);

        $response = $this->getJson('/api/classify?name=john');

        $response->assertStatus(200)
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'name',
                    'gender',
                    'probability',
                    'sample_size',
                    'is_confident',
                    'processed_at',
                ],
            ]);

        $processedAt = $response->json('data.processed_at');

        $this->assertTrue($response->json('data.is_confident'));

        $this->assertIso8601Utc($processedAt);
    }

    public function test_not_confident_result()
    {
        $this->fakeGenderizeResponse([
            'name' => 'Alex',
            'gender' => 'male',
            'probability' => 0.69,
            'count' => 99,
        ]);

        $response = $this->getJson('/api/classify?name=alex');

        $response->assertStatus(200);

        $this->assertFalse($response->json('data.is_confident'));
    }

    public function test_missing_name()
    {
        $response = $this->getJson('/api/classify');

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_empty_name()
    {
        $response = $this->getJson('/api/classify?name=');

        $response->assertStatus(400);
    }

    public function test_invalid_name_type()
    {
        $response = $this->getJson('/api/classify?' . http_build_query([
            'name' => ['john'],
        ]));

        $response->assertStatus(422);
    }

    public function test_no_prediction()
    {
        $this->fakeGenderizeResponse([
            'name' => 'unknown',
            'gender' => null,
            'probability' => 0.0,
            'count' => 120,
        ]);

        $response = $this->getJson('/api/classify?name=unknown');

        $response->assertStatus(422);
    }

    public function test_zero_sample_size()
    {
        $this->fakeGenderizeResponse([
            'name' => 'rare',
            'gender' => 'female',
            'probability' => 0.95,
            'count' => 0,
        ]);

        $response = $this->getJson('/api/classify?name=rare');

        $response->assertStatus(422);
    }

    public function test_external_api_failure()
    {
        $this->fakeGenderizeResponse(['error' => 'fail'], 500);

        $response = $this->getJson('/api/classify?name=john');

        $response->assertStatus(502);
    }

    public function test_timeout()
    {
        Http::fake([
            'https://api.genderize.io*' => Http::failedConnection(),
        ]);

        $response = $this->getJson('/api/classify?name=john');

        $response->assertStatus(502);
    }
}
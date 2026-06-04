<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{Patient, Screening, Answer, Result};
use Database\Seeders\DatabaseSeeder;

class PatientScreeningSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_expected_data(): void
    {
        // Run seeder
        $this->seed(DatabaseSeeder::class);

        // Verify patients count is 50
        $this->assertEquals(50, Patient::count());

        // Verify screenings, answers, and results exist
        $this->assertGreaterThan(0, Screening::count());
        $this->assertGreaterThan(0, Answer::count());
        $this->assertGreaterThan(0, Result::count());

        // Verify relationship structure and results format
        $firstResult = Result::first();
        $this->assertNotNull($firstResult);
        $this->assertContains($firstResult->rekomendasi, ['R16', 'R17', 'R18']);
    }
}

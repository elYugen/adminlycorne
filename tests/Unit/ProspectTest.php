<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Prospect;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProspectTest extends TestCase
{
    use RefreshDatabase;

    // test de l'index 
    public function test_it_displays_prospects_list(): void
    {
        Prospect::factory()->count(3)->create();

        $response = $this->get(route('prospect.index'));

        $response->assertStatus(200);
        $response->assertViewIs('prospect.index');
        $response->assertViewHas('prospects');
    }

    // test de la création de prospect
    public function test_it_creates_a_prospect(): void
    {
        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'johndoe@example.com',
            'phone_number' => '+123456789',
            'gender' => 'male',
            'address' => '123 Main St',
            'postal_code' => '12345',
            'city' => 'Paris',
            'company' => 'MyCompany',
            'siret' => '12345678901234',
        ];

        $response = $this->post(route('prospect.create'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Prospect créé avec succès',
        ]);

        $this->assertDatabaseHas('bc_prospects', $payload);
    }

    // test de la création via bon de commande
    public function test_it_validates_required_fields(): void
    {
        $response = $this->post(route('prospect.create'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'phone_number', 'address', 'postal_code', 'city']);
    }

    // test de maj d'un prospect
    public function test_it_updates_a_prospect(): void
    {
        $prospect = Prospect::factory()->create();

        $payload = [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'janesmith@example.com',
            'phone_number' => '+987654321',
            'gender' => 'female',
            'address' => '456 Elm St',
            'postal_code' => '54321',
            'city' => 'Lyon',
        ];

        $response = $this->put(route('prospect.update', $prospect->id), $payload);

        $response->assertStatus(302);
        $response->assertRedirect(route('prospect.index'));

        $this->assertDatabaseHas('bc_prospects', array_merge(['id' => $prospect->id], $payload));
    }

    // test de suppression de prospect
    public function test_it_deletes_a_prospect(): void
    {
        $prospect = Prospect::factory()->create();

        $response = $this->delete(route('prospect.delete', $prospect->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('prospect.index'));

        $this->assertDatabaseMissing('bc_prospects', ['id' => $prospect->id]);
    }

    // test de recherche de prospect
    public function test_it_searches_for_prospects(): void
    {
        $prospect = Prospect::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);

        $response = $this->get(route('prospect.search', ['query' => 'John']));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);
    }
}
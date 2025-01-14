<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BcUtilisateur;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    // test de l'index
    public function test_it_displays_users_list()
    {
        BcUtilisateur::factory()->count(3)->create();

        $response = $this->get(route('user.index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas('users');
    }

    // test de la création
    public function test_it_creates_a_user()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'role' => 'revendeur',
        ];

        $response = $this->post(route('user.create'), $payload);

        $response->assertStatus(302);
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('success', 'Utilisateur crée avec succès');

        $this->assertDatabaseHas('bc_utilisateurs', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'role' => 'revendeur',
        ]);

        $user = BcUtilisateur::where('email', 'johndoe@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    // test de validation lors de la création
    public function test_it_validates_required_fields_on_create()
    {
        $response = $this->post(route('user.create'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    // test de la maj d'un utilisateur
    public function test_it_updates_a_user()
    {
        $user = BcUtilisateur::factory()->create();

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'role' => 'administrateur',
        ];

        $response = $this->put(route('user.edit', $user->id), $payload);

        $response->assertStatus(302);
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('success', 'Utilisateur mis à jour avec succès');

        $this->assertDatabaseHas('bc_utilisateurs', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'role' => 'administrateur',
        ]);
    }

    // test de suppression
    public function test_it_deletes_a_user()
    {
        $user = BcUtilisateur::factory()->create();

        $response = $this->delete(route('user.delete', $user->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('success', 'Utilisateur supprimé avec succès');

        $this->assertDatabaseMissing('bc_utilisateurs', ['id' => $user->id]);
    }

    // test que l'email est unique lors de la maj
    public function test_it_validates_unique_email_on_update()
    {
        $existingUser = BcUtilisateur::factory()->create();
        $userToUpdate = BcUtilisateur::factory()->create();

        $payload = [
            'name' => 'Updated User',
            'email' => $existingUser->email, // mail existant
            'role' => 'administrateur',
        ];

        $response = $this->put(route('user.edit', $userToUpdate->id), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }
}
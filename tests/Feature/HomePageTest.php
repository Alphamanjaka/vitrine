<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    /**
     * Vérifie que la page d'accueil est accessible et renvoie le bon code de statut.
     */
    public function test_the_homepage_returns_a_successful_response(): void
    {
        // Simule une requête GET sur la racine du site
        $response = $this->get('/');

        // Vérifie que le serveur répond avec un code 200 (OK)
        $response->assertStatus(200);

        // Optionnel : Vérifie que la vue retournée est bien 'welcome'
        $response->assertViewIs('welcome');
    }
}

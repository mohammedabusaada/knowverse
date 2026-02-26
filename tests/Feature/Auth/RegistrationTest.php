<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Matched the fields to the actual implementation in RegisteredUserController
    $response = $this->post('/register', [
        'username' => 'testuser99',
        'full_name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Our controller redirects to login, NOT auto-login, so the user should still be a guest here
    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
<?php

namespace Tests;

use App\User;

class AuthTest extends TestCase
{
    public function testLogin()
    {
        $user = factory(User::class)->create([
            'email' => 'foo@example.com',
            'password' => bcrypt('bar'),
        ]);

        $this->json(
            'POST',
            'api/v1/login',
            ['email' => 'foo@example.com', 'password' => 'bar']
        );

        $this->seeJsonStructure(['token']);
    }

    public function testLoginFailure()
    {
        $this->json(
            'POST',
            'api/v1/login', ['email' => 'biz@example.com', 'password' => 'baz']
        );

        $this->assertResponseStatus(401);
    }

    public function testLogout()
    {
        $user = factory(User::class)->create();

        $this->json(
            'POST',
            'api/v1/logout',
            [],
            ['Authorization' => "Bearer {$user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($user->fresh()->api_token);
    }
}

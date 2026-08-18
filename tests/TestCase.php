<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Every user references the roles table via a foreign key, but RefreshDatabase
        // migrates without seeding. Ensure the three fixed roles (1=user, 2=admin,
        // 3=moderator) exist before any factory creates a user.
        if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->seed(\Database\Seeders\RoleSeeder::class);
        }
    }
}

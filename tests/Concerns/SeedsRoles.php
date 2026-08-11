<?php


namespace Tests\Concerns;

use Database\Seeders\RoleSeeder;

trait SeedsRoles
{
    protected function setUpSeedsRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }
}

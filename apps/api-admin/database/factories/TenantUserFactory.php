<?php

namespace Database\Factories;

use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantUser>
 */
class TenantUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'role' => TenantUserRole::MobileUser,
            'status' => TenantUserStatus::Active,
            'is_current' => false,
            'accepted_at' => now(),
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_current' => true,
        ]);
    }

    public function role(TenantUserRole $role): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => $role,
        ]);
    }

    public function invited(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantUserStatus::Invited,
            'invited_at' => now(),
            'accepted_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantUserStatus::Suspended,
            'suspended_at' => now(),
        ]);
    }
}

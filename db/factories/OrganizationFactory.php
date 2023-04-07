<?php

declare(strict_types=1);

namespace Database\Factories\Engelsystem\Models;

use Engelsystem\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /** @var string */
    protected $model = Organization::class; // phpcs:ignore

    public function definition(): array
    {
        return [
            'name'            => $this->faker->unique()->word(),
            'description'     => $this->faker->text(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->optional(.2)->phoneNumber(),
            'contact_person'  => $this->faker->text(),
        ];
    }
}
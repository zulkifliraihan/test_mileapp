<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $statuses = ['open', 'in_progress', 'done'];
        $createdAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $due = $this->faker->optional(0.8)->dateTimeBetween('now', '+30 days');

        return [
            'title' => $this->faker->sentence(6, true),
            'description' => $this->faker->paragraphs(mt_rand(1, 3), true),
            'status' => $this->faker->randomElement($statuses),
            'due_date' => $due,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}


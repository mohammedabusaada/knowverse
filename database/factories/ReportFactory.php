<?php

namespace Database\Factories;

use App\Models\{Report, User, Post, Comment};
use App\Enums\{ReportReason, ReportStatus};
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $targetType = $this->faker->randomElement([Post::class, Comment::class]);
        $target = $targetType::inRandomOrder()->first() ?? $targetType::factory();
        $status = $this->faker->randomElement(ReportStatus::cases());

        return [
            'reporter_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'target_id'   => $target->id,
            'target_type' => $targetType,
            'reason_type' => $this->faker->randomElement(ReportReason::cases()),
            'reason'      => $this->faker->sentence(),
            'status'      => $status,
            'resolved_by' => $status !== ReportStatus::PENDING ? User::factory() : null,
            'resolved_at' => $status !== ReportStatus::PENDING ? now() : null,
            'created_at'  => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
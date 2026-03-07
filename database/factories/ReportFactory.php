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
        // Dynamically resolve the polymorphic target entity
        $targetType = $this->faker->randomElement([Post::class, Comment::class, User::class]);
        $target = $targetType::inRandomOrder()->first() ?? $targetType::factory();
        
        // Simulate the moderation lifecycle state machine
        $status = $this->faker->randomElement(ReportStatus::cases());

        // Contextual Reason Resolution: Ensure the violation type logically matches the reported entity
        if ($targetType === User::class) {
            $reasonType = $this->faker->randomElement([ReportReason::HARASSMENT, ReportReason::IMPERSONATION]);
        } else {
            $reasonType = $this->faker->randomElement([ReportReason::SPAM, ReportReason::INAPPROPRIATE_CONTENT, ReportReason::HATE_SPEECH]);
        }

        return [
            'reporter_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'target_id'   => $target->id,
            'target_type' => $targetType,
            'reason_type' => $reasonType,
            'reason'      => $this->faker->sentence(),
            'status'      => $status,
            
            // Record the administrative audit trail only if the ticket is no longer pending
            'resolved_by' => $status !== ReportStatus::PENDING ? User::factory() : null,
            'resolved_at' => $status !== ReportStatus::PENDING ? now() : null,
            'created_at'  => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
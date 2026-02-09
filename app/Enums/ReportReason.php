<?php

namespace App\Enums;

enum ReportReason: string
{
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case HATE_SPEECH = 'hate_speech';
    case VIOLENCE = 'violence'; // Added to match the Moderation Service logic
    case MISINFORMATION = 'misinformation';
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';
    case COPYRIGHT = 'copyright';
    case IMPERSONATION = 'impersonation';
    case OTHER = 'other';

    /**
     * Label shown to users in dropdowns/tables
     */
    public function label(): string
    {
        return match ($this) {
            self::SPAM => 'Spam or advertising',
            self::HARASSMENT => 'Harassment or bullying',
            self::HATE_SPEECH => 'Hate speech',
            self::VIOLENCE => 'Violence or physical harm',
            self::MISINFORMATION => 'False or misleading information',
            self::INAPPROPRIATE_CONTENT => 'Inappropriate content',
            self::COPYRIGHT => 'Copyright violation',
            self::IMPERSONATION => 'Impersonation or fake account',
            self::OTHER => 'Other',
        };
    }

    /**
     * Optional: Longer description for tooltips or modal details
     */
    public function description(): string
    {
        return match ($this) {
            self::SPAM => 'Repetitive content, scams, or commercial solicitation.',
            self::VIOLENCE => 'Threats of violence or promotion of self-harm.',
            self::HATE_SPEECH => 'Attack on protected groups based on race, religion, etc.',
            default => 'This content violates community guidelines.',
        };
    }

    /**
     * Reasons allowed per target type
     */
    public static function for(string $targetType): array
    {
        // Normalize the target type (e.g., from Post::class to 'post')
        $type = strtolower(class_basename($targetType));

        return match ($type) {
            'user' => [
                self::HARASSMENT,
                self::HATE_SPEECH,
                self::IMPERSONATION,
                self::OTHER,
            ],
            default => self::cases(),
        };
    }
}
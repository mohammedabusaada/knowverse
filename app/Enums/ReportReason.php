<?php

namespace App\Enums;

/**
 * Defines the standard set of violation reasons across the platform.
 * Backed Enums guarantee data integrity at the application layer before interacting 
 * with the database, preventing injection of invalid report categories.
 */
enum ReportReason: string
{
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case HATE_SPEECH = 'hate_speech';
    case VIOLENCE = 'violence';
    case MISINFORMATION = 'misinformation';
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';
    case COPYRIGHT = 'copyright';
    case IMPERSONATION = 'impersonation';
    case OTHER = 'other';

    /**
     * Provides a human-readable, presentation-ready label for the frontend UI.
     * * @return string
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
     * Provides detailed contextual descriptions for moderation guidelines 
     * and user tooltips during the reporting process.
     * * @return string
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
     * Dynamically filters available report reasons based on the target entity context.
     * This prevents illogical reports (e.g., reporting a User Profile for 'Copyright violation'), 
     * maintaining a coherent User Experience (UX).
     * * @param string $targetType The fully qualified class name or morph map alias.
     * @return array<ReportReason>
     */
    public static function for(string $targetType): array
    {
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
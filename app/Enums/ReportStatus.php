<?php

namespace App\Enums;

/**
 * Represents the lifecycle state machine of a moderation report.
 * Provides explicit states for the administrative workflow and dashboard filtering.
 */
enum ReportStatus: string
{
    case PENDING   = 'pending';   // In queue, awaiting administrative review
    case RESOLVED  = 'resolved';  // Reviewed, validated, and penal action applied
    case DISMISSED = 'dismissed'; // Reviewed, but found to not violate guidelines
}
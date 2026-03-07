<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

/**
 * Secures the moderation reporting infrastructure.
 * Enforces strict Role-Based Access Control (RBAC) to protect sensitive oversight queues.
 */
class ReportPolicy
{
    /**
     * Allow admins and moderators to see the list of reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->canModerate();
    }

    /**
     * Allow admins and moderators to view a specific report.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->canModerate();
    }

    /**
     * General permission for moderation actions.
     */
    public function manageReports(User $user): bool
    {
        return $user->canModerate();
    }

    /**
     * Any authenticated user can create a report.
     */
    public function create(User $user): bool 
    { 
        return true; 
    }

    /**
     * Only admins and moderators can update or delete report records.
     */
    public function update(User $user, Report $report): bool 
    { 
        return $user->canModerate(); 
    }

    public function delete(User $user, Report $report): bool 
    { 
        return $user->canModerate(); 
    }
}
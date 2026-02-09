<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Allow admins to see the list of reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Allow admins to view a specific report.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    /**
     * General permission for moderation actions.
     */
    public function manageReports(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Any authenticated user can create a report.
     */
    public function create(User $user): bool 
    { 
        return true; 
    }

    /**
     * Only admins can update or delete report records.
     */
    public function update(User $user, Report $report): bool { return $user->isAdmin(); }
    public function delete(User $user, Report $report): bool { return $user->isAdmin(); }
}
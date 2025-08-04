<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class FilamentGroupingHelper
{
    /**
     * Determines if navigation groups should be used based on the user's role
     * 
     * For Super Admin, navigation groups are maintained
     * For Content Management and Customer Service roles, navigation groups are disabled
     * 
     * @param string $defaultGroup The default navigation group for the resource
     * @return string|null Returns the navigation group or null to disable grouping
     */
    public static function getNavigationGroup(string $defaultGroup): ?string
    {
        $user = Auth::user();

        // If user is not authenticated, return null (should not happen in Filament context)
        if (!$user) {
            return null;
        }

        // For Super Admin, keep the navigation group
        if ($user->hasRole('super_admin')) {
            return $defaultGroup;
        }

        $roles = [
            'super_admin',
            'Content Management',
            'Customer Service',
            'director'
        ];

        // Check if user has multiple roles
        $userRoleCount = 0;
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $userRoleCount++;
            }
        }

        // For any user with multiple roles, keep navigation group
        if ($userRoleCount > 1) {
            return $defaultGroup;
        }

        // For Content Management and Customer Service roles with single role, disable grouping
        if ($user->hasRole('Content Management') || $user->hasRole('Customer Service')) {
            return null;
        }
        ;

        // For other roles, return the default group
        return $defaultGroup;
    }
}
<?php

namespace App\Enums;

/**
 * Constrains the polymorphic entities that are eligible to be flagged by the reporting system.
 * These string values must map directly to the Eloquent morphMap definitions in the AppServiceProvider 
 * to prevent arbitrary or malicious class instantiation.
 */
enum ReportTargetType: string
{
    case POST    = 'post';
    case COMMENT = 'comment';
    case USER    = 'user';
}
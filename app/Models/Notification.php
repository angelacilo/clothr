<?php

/**
 * FILE: Notification.php
 * 
 * What this file is:
 * This model represents a general notification (usually for the Admin).
 * When someone registers, places an order, or leaves a review, a 
 * notification is created so the Admin knows what's happening.
 * 
 * How it connects to the project:
 * - Controllers call the "createNotification" helper method.
 * - The Admin Dashboard shows these notifications in a dropdown list.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Details about the event that happened.
     */
    protected $fillable = [
        'type', 'title', 'message', 'link', 'is_read'
    ];

    /**
     * CASTS: Converts 0/1 to false/true.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * SCOPE: This is a custom shortcut for database queries.
     * Instead of writing ->where('is_read', false) every time, 
     * we can just write ->unread().
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * HELPER METHOD: A quick way to create a notification from anywhere.
     * 
     * @param string $type — e.g., 'new_order'
     * @param string $title — e.g., 'New Order Placed'
     * @param string $message — Details about the event
     * @param string|null $link — Where the admin should go when they click it
     */
    public static function createNotification($type, $title, $message, $link = null)
    {
        return self::create([
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => false,
        ]);
    }
}

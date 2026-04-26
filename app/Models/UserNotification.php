<?php

/**
 * FILE: UserNotification.php
 * 
 * What this file is:
 * This model handles notifications that are sent to specific people 
 * (like a Customer, a Courier, or a Rider).
 * For example, a customer gets a notification when their order is "Shipped".
 * 
 * How it connects to the project:
 * - It is used whenever the order status changes.
 * - Each user has their own personal notification list.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Details about who gets the notification and why.
     */
    protected $fillable = [
        'user_id', 'order_id', 'type', 'title', 'message', 'link', 'is_read'
    ];

    /**
     * CASTS: Converts 0/1 into false/true.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * RELATIONSHIP: This notification belongs to a specific User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATIONSHIP: This notification is usually about a specific Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * SCOPE: A shortcut to find only notifications that haven't been seen yet.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * HELPER METHOD: A quick way to send a message to a specific person.
     * 
     * @param int $userId — The person who should see this
     * @param int $orderId — The order this message is about
     * @param string $type — e.g., 'status_update'
     * @param string $title — The header of the message
     * @param string $message — The actual text
     * @param string $link — Where the user goes when they click it
     */
    public static function notify($userId, $orderId, $type, $title, $message, $link)
    {
        // Don't do anything if there is no user (e.g., guest checkouts).
        if (!$userId) return null;
        
        return self::create([
            'user_id'  => $userId,
            'order_id' => $orderId,
            'type'     => $type,
            'title'    => $title,
            'message'  => $message,
            'link'     => $link,
            'is_read'  => false,
        ]);
    }
}

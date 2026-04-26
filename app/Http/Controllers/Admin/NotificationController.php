<?php

/**
 * FILE: Admin/NotificationController.php
 * 
 * What this file does:
 * This controller handles real-time notifications for the admin.
 * When a new order is placed, a notification is saved to the database.
 * This controller allows the admin dashboard to fetch those notifications 
 * and mark them as "read".
 * 
 * How it connects to the project:
 * - It is called via JavaScript (AJAX) from the admin layout.
 * - It uses the Notification model to manage unread messages.
 * - It returns JSON data (text data) instead of HTML pages.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetches all unread notifications.
     * 
     * This is used by the notification bell in the sidebar.
     * It formats the date so it says things like "5 minutes ago".
     * 
     * @return json — list of unread notifications
     */
    public function index()
    {
        // Get unread notifications, newest first.
        $notifications = Notification::unread()
            ->latest()
            ->get()
            ->map(function ($notification) {
                // We format the data nicely for the JavaScript to read.
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'is_read' => $notification->is_read,
                    // diffForHumans() turns a timestamp into "2 hours ago".
                    'created_at' => $notification->created_at->diffForHumans()
                ];
            });

        // Return the data as a JSON response.
        return response()->json($notifications);
    }

    /**
     * Marks a specific notification as "Read" when clicked.
     * 
     * @param int $id — the ID of the notification
     * @return json — success message
     */
    public function markAsRead($id)
    {
        // Find the notification in the table.
        $notification = Notification::findOrFail($id);
        
        // Update the "is_read" column to true.
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Marks every unread notification as "Read" at once.
     * 
     * @return json — success message
     */
    public function markAllAsRead()
    {
        // Find all notifications that are unread and update them all at once.
        Notification::unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}

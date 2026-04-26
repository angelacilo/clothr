<?php

/**
 * FILE: User.php
 * 
 * What this file is:
 * This is the "Blueprint" for a User in the system.
 * It represents anyone who can log in, whether they are a Customer, 
 * an Admin, a Courier, or a Rider.
 * 
 * How it connects to the project:
 * - It is the core model used for Authentication (Logging in).
 * - It links to almost everything else (Orders, Addresses, Reviews, etc.).
 */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * FILLABLE: These are the columns we can save data into from a form.
     * It's like a "Whitelist" of allowed fields.
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin', 'role', 'phone', 'avatar', 'bio'
    ];

    /**
     * HIDDEN: These fields are secret. 
     * When we send user data to JavaScript or an API, we HIDE the password 
     * so it never leaves the server.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * CASTS: Tells Laravel how to handle specific data types.
     * For example, 'is_admin' is stored as 1 or 0 in the DB, but 
     * Laravel converts it to a real true/false (boolean) for us.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'          => 'boolean',
    ];

    // RELATIONSHIPS (The connections between tables)

    /**
     * A User can have many saved Shipping Addresses.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * A User can place many Orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A User can have many items in their Wishlist.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * A User can write many Reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * A User has a shopping cart which can contain many items.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * If this user is a "Rider", they have a Rider profile.
     */
    public function rider() { 
        return $this->hasOne(Rider::class); 
    }

    /**
     * If this user is a "Courier" (Company), they have a Courier profile.
     */
    public function courierAccount() { 
        return $this->hasOne(Courier::class); 
    }
}

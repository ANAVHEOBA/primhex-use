<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_address',
        'description',
        'sender_name',
        'sender_address',
        'sender_phone',
        'item',
        'package_size',
        'delivery_type',
        'delivery_time',
        'pickup_time',
        'receiver_name',
        'receiver_address',
        'receiver_phone',
        'additional_info', // Add other relevant fields
    ];
}

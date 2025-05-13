<?php

namespace App\Models;

class BookingAssignmentModel extends Model
{
    protected $table = 'booking_assignment';
    protected $primaryKey = 'ba_id';

    protected $fillable = [
        'ba_booking_id',
        'ba_technician_id',
        'ba_assigned_at',
        'ba_status',
        'ba_notes',
        'ba_completed_at'
    ];

    protected $timestamps = true;
    protected $createdAtColumn = 'ba_assigned_at';
    protected $updatedAtColumn = null; // No updated_at column for this table

}
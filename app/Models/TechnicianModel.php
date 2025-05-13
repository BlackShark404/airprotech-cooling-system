<?php

namespace App\Models;

class TechnicianModel extends BaseModel
{
    protected $table = 'technician';
    protected $primaryKey = 'te_account_id';

    protected $fillable = [
        'te_account_id',
        'te_is_available'
    ];

}
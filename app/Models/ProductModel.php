<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    // Columns allowed for mass assignment (insert/update)
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity'
    ];

    // Columns searchable by DataTables global search
    protected $searchableFields = [
        'name',
        'description'
        // Add 'price::text' or 'stock_quantity::text' if you want to search them as text
        // Note: Searching numeric fields efficiently might require specific DB tuning or logic
    ];

    // Enable automatic timestamps (created_at, updated_at)
    protected $timestamps = true;
    protected $createdAtColumn = 'created_at';
    protected $updatedAtColumn = 'updated_at';

    // Enable soft deletes (deleted_at)
    protected $useSoftDeletes = true;
    protected $deletedAtColumn = 'deleted_at';

    public function __construct()
    {
        parent::__construct();
        // Ensure soft deletes are considered by default
        $this->whereSoftDeleted();
    }
}
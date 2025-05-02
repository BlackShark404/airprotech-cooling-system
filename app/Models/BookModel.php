<?php

namespace App\Models;

class BookModel extends BaseModel
{
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title', 
        'author', 
        'isbn', 
        'category', 
        'publication_year', 
        'publisher', 
        'description', 
        'in_stock', 
        'price'
    ];
    protected $searchableFields = [
        'title', 
        'author', 
        'isbn', 
        'category', 
        'publisher'
    ];
    protected $useSoftDeletes = true;
    protected $timestamps = true;
    protected $createdAtColumn = 'created_at';
    protected $updatedAtColumn = 'updated_at';
    protected $deletedAtColumn = 'deleted_at';
    
    /**
     * Get books for datatables
     */
    public function getDataTablesData($start, $length, $search, $order)
    {
        // Start building the query
        $this->select('*');
        
        // Apply search if provided
        if (!empty($search)) {
            $this->whereGroup();
            foreach ($this->searchableFields as $field) {
                $this->whereLike($field, "%$search%");
                $this->orWhere();
            }
            $this->endWhereGroup();
        }
        
        // Apply order
        if (!empty($order)) {
            $column = $order['column'] ?? 'id';
            $direction = $order['dir'] ?? 'asc';
            $this->orderBy("$column $direction");
        } else {
            $this->orderBy('id DESC');
        }
        
        // Get total count for pagination
        $total = $this->count();
        
        // Apply limit and offset
        $this->limit($length);
        $this->offset($start);
        
        // Get the data
        $data = $this->get();
        
        return [
            'data' => $data,
            'total' => $total
        ];
    }
}
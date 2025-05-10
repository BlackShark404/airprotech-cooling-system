<?php

namespace App\Models;

class ProductVariantModel extends BaseModel
{
    // Table name
    protected $table = 'product_variant';
    protected $primaryKey = 'var_id';
    
    // Fillable fields
    protected $fillable = [
        'prod_id',
        'var_capacity',
        'var_srp_price',
        'var_price_free_install',
        'var_price_with_install',
        'var_power_consumption'
    ];
    
    // Get all variants
    public function getAllVariants()
    {
        return $this->all();
    }
    
    // Find variant by ID
    public function findById($id)
    {
        return $this->find($id);
    }
    
    // Get variants by product ID
    public function getVariantsByProduct($productId)
    {
        return $this->where('prod_id = :prod_id')
                    ->bind(['prod_id' => $productId])
                    ->get();
    }
    
    // Create a new variant
    public function createVariant($data)
    {
        return $this->insert($data);
    }
    
    // Update a variant
    public function updateVariant($variantId, $data)
    {
        return $this->update(
            $data,
            "{$this->primaryKey} = :id",
            ['id' => $variantId]
        );
    }
    
    // Delete a variant
    public function deleteVariant($variantId)
    {
        return $this->delete(
            "{$this->primaryKey} = :id",
            ['id' => $variantId]
        );
    }
    
    // Get variants with product info
    public function getVariantsWithProductInfo()
    {
        return $this->select('pv.*, p.prod_name, p.prod_image, p.prod_availability_status')
                    ->join('product p', 'pv.prod_id', 'p.prod_id')
                    ->orderBy('p.prod_name, pv.var_capacity')
                    ->get();
    }
    
    // Get variants with inventory count
    public function getVariantsWithInventoryCount()
    {
        return $this->db->query("
            SELECT 
                pv.*,
                p.prod_name,
                p.prod_image,
                p.prod_availability_status,
                COALESCE(SUM(i.quantity), 0) as total_quantity
            FROM 
                product_variant pv
            JOIN 
                product p ON pv.prod_id = p.prod_id
            LEFT JOIN 
                inventory i ON pv.prod_id = i.prod_id
            GROUP BY 
                pv.var_id, p.prod_name, p.prod_image, p.prod_availability_status
            ORDER BY 
                p.prod_name, pv.var_capacity
        ")->fetchAll();
    }
}
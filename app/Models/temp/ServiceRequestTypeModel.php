<?php

namespace App\Models;

class ServiceRequestTypeModel extends BaseModel
{
    protected $table = 'service_type';
    protected $primaryKey = 'st_id';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'st_code',
        'st_name',
        'st_description',
        'st_is_active'
    ];

    // Enable timestamps
    protected $timestamps = true;
    protected $createdAtColumn = 'st_created_at';
    protected $updatedAtColumn = 'st_updated_at';

    /**
     * Get all active service types
     * 
     * @return array Array of active service types
     */
    public function getActiveServiceTypes()
    {
        return $this->where("st_is_active = :isActive")
                ->orderBy("st_name ASC")
                ->bind(['isActive' => true])
                ->get();

    }
    
    /**
     * Get a service type by code
     * 
     * @param string $code The service type code
     * @return array|null The service type or null if not found
     */
    public function getServiceTypeByCode($code)
    {
        return $this->where("st_code = :code")
                ->bind(['code' => $code])
                ->first();

    }
    
    /**
     * Create a new service type
     * 
     * @param array $data Service type data
     * @return bool Success status
     */
    public function createServiceType($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Update a service type
     * 
     * @param int $typeId The service type ID
     * @param array $data Updated service type data
     * @return bool Success status
     */
    public function updateServiceType($typeId, $data)
    {
        return $this->update(
            $data,
            "st_id = :typeId",
            ['typeId' => $typeId]
        );

    }
    
    /**
     * Toggle the active status of a service type
     * 
     * @param int $typeId The service type ID
     * @param bool $isActive New active status
     * @return bool Success status
     */
    public function toggleServiceTypeStatus($typeId, $isActive)
    {
        return $this->update(
            ['st_is_active' => $isActive],
            "st_id = :typeId",
            ['typeId' => $typeId]
        );
    }
}
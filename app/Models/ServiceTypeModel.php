<?php

namespace App\Models;

class ServiceTypeModel extends BaseModel
{
    protected $table = 'service_type';
    
    /**
     * Get all active service types
     * 
     * @return array Array of active service types
     */
    public function getActiveServiceTypes()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE st_is_active = :isActive 
                ORDER BY st_name ASC";
                
        return $this->query($sql, ['isActive' => true]);
    }
    
    /**
     * Get a service type by code
     * 
     * @param string $code The service type code
     * @return array|null The service type or null if not found
     */
    public function getServiceTypeByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE st_code = :code";
                
        return $this->queryOne($sql, ['code' => $code]);
    }
    
    /**
     * Create a new service type
     * 
     * @param array $data Service type data
     * @return bool Success status
     */
    public function createServiceType($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) 
                VALUES ($placeholders)";
                
        return $this->execute($sql, $data) > 0;
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
        $updates = [];
        foreach (array_keys($data) as $column) {
            $updates[] = "$column = :$column";
        }
        $setClause = implode(', ', $updates);
        
        $sql = "UPDATE {$this->table} 
                SET $setClause 
                WHERE st_id = :typeId";
                
        $params = array_merge($data, ['typeId' => $typeId]);
        
        return $this->execute($sql, $params) > 0;
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
        $sql = "UPDATE {$this->table} 
                SET st_is_active = :isActive 
                WHERE st_id = :typeId";
                
        return $this->execute($sql, [
            'isActive' => $isActive,
            'typeId' => $typeId
        ]) > 0;
    }
}
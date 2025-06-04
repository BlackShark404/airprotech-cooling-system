<?php

namespace App\Models;

class TechnicianModel extends Model
{
    protected $table = 'technician';
    protected $primaryKey = 'te_account_id';

    protected $fillable = [
        'te_account_id',
        'te_is_available'
    ];
    
    /**
     * Update a technician record
     * 
     * @param array $data Data to update
     * @param string $where Where clause
     * @param array $params Additional parameters for the where clause
     * @return bool Success status
     */
    public function update($data, $where, $params = [])
    {
        $formattedUpdate = $this->formatUpdateData($data);
        
        if (empty($formattedUpdate['updateClause'])) {
            return true; // No data to update
        }
        
        $sql = "UPDATE {$this->table} SET {$formattedUpdate['updateClause']} WHERE {$where}";
        $allParams = array_merge($formattedUpdate['filteredData'], $params);
        
        return $this->execute($sql, $allParams) !== false;
    }
    
    /**
     * Get all available technicians
     * 
     * @return array List of available technicians
     */
    public function getAvailableTechnicians()
    {
        $sql = "SELECT t.*, 
                u.ua_first_name, u.ua_last_name, u.ua_email, u.ua_phone_number,
                CONCAT(u.ua_first_name, ' ', u.ua_last_name) as full_name
                FROM {$this->table} t
                INNER JOIN user_account u ON t.te_account_id = u.ua_id
                WHERE t.te_is_available = true
                AND u.ua_is_active = true
                ORDER BY u.ua_first_name, u.ua_last_name";
                
        return $this->query($sql);
    }
    
    /**
     * Create a new assignment for a technician
     * 
     * @param array $data Assignment data
     * @return int|bool The ID of the new assignment or false on failure
     */
    public function createAssignment($data)
    {
        // Check if this is a service booking or product booking
        if (isset($data['ba_booking_id'])) {
            // Service booking assignment
            $bookingAssignmentModel = new BookingAssignmentModel();
            return $bookingAssignmentModel->addAssignment($data);
        } else if (isset($data['pa_order_id'])) {
            // Product booking assignment
            $productAssignmentModel = new ProductAssignmentModel();
            return $productAssignmentModel->createAssignment($data);
        }
        
        return false;
    }
}
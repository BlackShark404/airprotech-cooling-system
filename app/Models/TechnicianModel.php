<?php

namespace App\Models;

class TechnicianModel extends BaseModel
{
    protected $table = 'technician';
    
    /**
     * Get all active technicians
     * 
     * @return array Array of active technicians
     */
    public function getActiveTechnicians()
    {
        $sql = "SELECT t.*, ua.ua_first_name, ua.ua_last_name, ua.ua_email, ua.ua_phone_number
                FROM {$this->table} t
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                WHERE t.te_is_available = true
                AND ua.ua_is_active = true
                AND ua.ua_deleted_at IS NULL
                ORDER BY ua.ua_first_name ASC, ua.ua_last_name ASC";
                
        return $this->query($sql);
    }
    
    /**
     * Get a technician by ID
     * 
     * @param int $techId The technician ID (account_id)
     * @return array|null The technician or null if not found
     */
    public function getTechnicianById($techId)
    {
        $sql = "SELECT t.*, ua.ua_first_name, ua.ua_last_name, ua.ua_email, ua.ua_phone_number
                FROM {$this->table} t
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                WHERE t.te_account_id = :techId
                AND ua.ua_deleted_at IS NULL";
                
        return $this->queryOne($sql, ['techId' => $techId]);
    }
    
    /**
     * Get technicians with their assignment counts
     * 
     * @return array Array of technicians with assignment counts
     */
    public function getTechniciansWithAssignmentCounts()
    {
        $sql = "SELECT t.*, 
                ua.ua_first_name, 
                ua.ua_last_name, 
                ua.ua_email, 
                ua.ua_phone_number,
                COUNT(ba.ba_id) as assignment_count 
                FROM {$this->table} t
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                LEFT JOIN booking_assignment ba ON t.te_account_id = ba.ba_technician_id
                AND ba.ba_status != 'unassigned'
                AND ba.ba_completed_at IS NULL
                WHERE ua.ua_deleted_at IS NULL
                GROUP BY t.te_account_id, ua.ua_id
                ORDER BY ua.ua_first_name ASC, ua.ua_last_name ASC";
                
        return $this->query($sql);
    }
    
    /**
     * Get available technicians for a given date
     * 
     * @param string $date The date to check availability (YYYY-MM-DD)
     * @return array Array of available technicians
     */
    public function getAvailableTechnicians($date)
    {
        $sql = "SELECT t.*, ua.ua_first_name, ua.ua_last_name, ua.ua_email, ua.ua_phone_number
                FROM {$this->table} t
                JOIN user_account ua ON t.te_account_id = ua.ua_id
                WHERE t.te_is_available = true
                AND ua.ua_is_active = true
                AND ua.ua_deleted_at IS NULL
                AND t.te_account_id NOT IN (
                    SELECT ba.ba_technician_id
                    FROM booking_assignment ba
                    JOIN service_booking sb ON ba.ba_booking_id = sb.sb_id
                    WHERE ba.ba_status != 'unassigned'
                    AND sb.sb_requested_date = :date
                    AND sb.sb_status NOT IN ('completed', 'cancelled')
                    AND sb.sb_deleted_at IS NULL
                    AND ba.ba_completed_at IS NULL
                )
                ORDER BY ua.ua_first_name ASC, ua.ua_last_name ASC";
                
        return $this->query($sql, ['date' => $date]);
    }
    
    /**
     * Create a new technician
     * 
     * @param array $data Technician data
     * @return bool Success status
     */
    public function createTechnician($data)
    {
        $formatted = $this->formatInsertData($data);
        
        $sql = "INSERT INTO {$this->table} ({$formatted['columns']}) 
                VALUES ({$formatted['placeholders']})";
                
        return $this->execute($sql, $formatted['filteredData']) > 0;
    }
    
    /**
     * Update a technician
     * 
     * @param int $techId The technician account ID
     * @param array $data Updated technician data
     * @return bool Success status
     */
    public function updateTechnician($techId, $data)
    {
        $formatted = $this->formatUpdateData($data);
        
        $sql = "UPDATE {$this->table} 
                SET {$formatted['updateClause']} 
                WHERE te_account_id = :techId";
                
        $params = array_merge($formatted['filteredData'], ['techId' => $techId]);
        
        return $this->execute($sql, $params) > 0;
    }
    
    /**
     * Toggle the availability status of a technician
     * 
     * @param int $techId The technician account ID
     * @param bool $isAvailable New availability status
     * @return bool Success status
     */
    public function toggleTechnicianAvailability($techId, $isAvailable)
    {
        $sql = "UPDATE {$this->table} 
                SET te_is_available = :isAvailable 
                WHERE te_account_id = :techId";
                
        return $this->execute($sql, [
            'isAvailable' => $isAvailable,
            'techId' => $techId
        ]) > 0;
    }
}
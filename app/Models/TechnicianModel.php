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

    /**
     * Get all technicians with account details
     * 
     * @return array Array of technician records with account details
     */
    public function getAllTechnicians()
    {
        return $this->select('
            technician.*, 
            user_account.ua_first_name, 
            user_account.ua_last_name, 
            user_account.ua_email, 
            user_account.ua_phone_number,
            user_account.ua_profile_url,
            user_account.ua_address,
            user_account.ua_is_active
        ')
        ->join('user_account', 'technician.te_account_id', 'user_account.ua_id')
        ->orderBy('user_account.ua_last_name, user_account.ua_first_name')
        ->get();
    }

    /**
     * Get technician by account ID
     * 
     * @param int $accountId Account ID
     * @return array|null Technician record or null if not found
     */
    public function getTechnicianByAccountId($accountId)
    {
        return $this->select('
            technician.*, 
            user_account.ua_first_name, 
            user_account.ua_last_name, 
            user_account.ua_email, 
            user_account.ua_phone_number,
            user_account.ua_profile_url,
            user_account.ua_address,
            user_account.ua_is_active
        ')
        ->join('user_account', 'technician.te_account_id', 'user_account.ua_id')
        ->where('technician.te_account_id = :account_id')
        ->bind(['account_id' => $accountId])
        ->first();
    }

    /**
     * Get all available technicians
     * 
     * @return array Array of available technician records
     */
    public function getAvailableTechnicians()
    {
        return $this->select('
            technician.*, 
            user_account.ua_first_name, 
            user_account.ua_last_name, 
            user_account.ua_email, 
            user_account.ua_phone_number,
            user_account.ua_profile_url,
            user_account.ua_address
        ')
        ->join('user_account', 'technician.te_account_id', 'user_account.ua_id')
        ->where('technician.te_is_available = :is_available')
        ->where('user_account.ua_is_active = :is_active')
        ->bind([
            'is_available' => true,
            'is_active' => true
        ])
        ->orderBy('user_account.ua_last_name, user_account.ua_first_name')
        ->get();
    }

    /**
     * Create a new technician
     * 
     * @param int $accountId Account ID
     * @param bool $isAvailable Availability status
     * @return bool Success status
     */
    public function createTechnician($accountId, $isAvailable = true)
    {
        return $this->insert([
            'te_account_id' => $accountId,
            'te_is_available' => $isAvailable
        ]);
    }

    /**
     * Update technician availability
     * 
     * @param int $accountId Account ID
     * @param bool $isAvailable New availability status
     * @return bool Success status
     */
    public function updateAvailability($accountId, $isAvailable)
    {
        return $this->update(
            ['te_is_available' => $isAvailable],
            "te_account_id = :account_id",
            ['account_id' => $accountId]
        );
    }

    /**
     * Get technician's current assignments
     * 
     * @param int $technicianId Technician ID
     * @return array Array of service bookings assigned to the technician
     */
    public function getCurrentAssignments($technicianId)
    {
        return $this->db->query("
            SELECT 
                ba.*,
                sb.sb_preferred_date,
                sb.sb_preferred_time,
                sb.sb_address,
                sb.sb_description,
                sb.sb_status as booking_status,
                sb.sb_priority,
                st.st_name as service_type_name,
                CONCAT(ua.ua_first_name, ' ', ua.ua_last_name) as customer_name,
                ua.ua_phone_number as customer_phone
            FROM booking_assignment ba
            JOIN service_booking sb ON ba.ba_booking_id = sb.sb_id
            JOIN service_type st ON sb.sb_service_type_id = st.st_id
            JOIN user_account ua ON sb.sb_customer_id = ua.ua_id
            WHERE ba.ba_technician_id = :technician_id
            AND ba.ba_status IN ('assigned', 'in-progress')
            ORDER BY sb.sb_preferred_date, sb.sb_preferred_time
        ", ['technician_id' => $technicianId])->fetchAll();
    }

    /**
     * Delete a technician
     * 
     * @param int $technicianId Technician ID
     * @return bool Success status
     */
    public function deleteTechnician($technicianId)
    {
        // First check if technician has any active assignments
        $activeAssignments = $this->db->query("
            SELECT COUNT(*) FROM booking_assignment 
            WHERE ba_technician_id = :technician_id 
            AND ba_status IN ('assigned', 'in-progress')
        ", ['technician_id' => $technicianId])->fetchColumn();

        if ($activeAssignments > 0) {
            // Technician has active assignments, cannot delete
            return false;
        }

        // Delete the technician
        return $this->delete("te_account_id = :technician_id", ['technician_id' => $technicianId]);
    }
    
    /**
     * Get technician performance statistics
     * 
     * @param int $technicianId Technician ID
     * @return array Technician statistics
     */
    public function getTechnicianStats($technicianId)
    {
        $stats = [];
        
        // Total assignments
        $stats['total_assignments'] = $this->db->query("
            SELECT COUNT(*) FROM booking_assignment 
            WHERE ba_technician_id = :technician_id
        ", ['technician_id' => $technicianId])->fetchColumn();
        
        // Completed assignments
        $stats['completed_assignments'] = $this->db->query("
            SELECT COUNT(*) FROM booking_assignment 
            WHERE ba_technician_id = :technician_id 
            AND ba_status = 'completed'
        ", ['technician_id' => $technicianId])->fetchColumn();
        
        // In-progress assignments
        $stats['in_progress_assignments'] = $this->db->query("
            SELECT COUNT(*) FROM booking_assignment 
            WHERE ba_technician_id = :technician_id 
            AND ba_status = 'in-progress'
        ", ['technician_id' => $technicianId])->fetchColumn();
        
        // Current workload
        $stats['current_workload'] = $this->db->query("
            SELECT COUNT(*) FROM booking_assignment 
            WHERE ba_technician_id = :technician_id 
            AND ba_status IN ('assigned', 'in-progress')
        ", ['technician_id' => $technicianId])->fetchColumn();
        
        // Completion rate
        if ($stats['total_assignments'] > 0) {
            $stats['completion_rate'] = round(($stats['completed_assignments'] / $stats['total_assignments']) * 100, 2);
        } else {
            $stats['completion_rate'] = 0;
        }
        
        return $stats;
    }
    
    /**
     * Get technicians with the lowest current workload
     * 
     * @param int $limit Maximum number of technicians to return
     * @return array Array of technicians with workload information
     */
    public function getTechniciansWithLowestWorkload($limit = 5)
    {
        return $this->db->query("
            SELECT 
                t.te_account_id,
                ua.ua_first_name,
                ua.ua_last_name,
                t.te_is_available,
                COUNT(ba.ba_id) as current_workload
            FROM technician t
            JOIN user_account ua ON t.te_account_id = ua.ua_id
            LEFT JOIN booking_assignment ba ON t.te_account_id = ba.ba_technician_id AND ba.ba_status IN ('assigned', 'in-progress')
            WHERE t.te_is_available = TRUE AND ua.ua_is_active = TRUE
            GROUP BY t.te_account_id, ua.ua_first_name, ua.ua_last_name, t.te_is_available
            ORDER BY current_workload ASC, ua.ua_last_name, ua.ua_first_name
            LIMIT :limit
        ", ['limit' => $limit])->fetchAll();
    }
}
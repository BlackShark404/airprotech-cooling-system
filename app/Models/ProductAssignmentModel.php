<?php

namespace App\Models;

class ProductAssignmentModel extends BaseModel
{
    protected $table = 'product_assignment';
    protected $primaryKey = 'pa_id';
    protected $useSoftDeletes = false;
    protected $timestamps = true;
    protected $createdAtColumn = 'pa_assigned_at';
    protected $updatedAtColumn = 'pa_updated_at';
    
    protected $fillable = [
        'pa_order_id',
        'pa_technician_id',
        'pa_status',
        'pa_notes',
        'pa_started_at',
        'pa_completed_at'
    ];

    /**
     * Get all product assignments for a specific technician
     * 
     * @param int $technicianId The technician ID
     * @return array The product assignments for the technician
     */
    public function getAssignmentsByTechnician($technicianId)
    {
        return $this->select('product_assignment.*, product_booking.pb_variant_id, 
                            product_booking.pb_quantity, product_booking.pb_unit_price,
                            product_booking.pb_total_amount, product_booking.pb_status as booking_status,
                            product_booking.pb_preferred_date, product_booking.pb_preferred_time,
                            product_booking.pb_address, product_booking.pb_description,
                            CONCAT(user_account.ua_first_name, \' \', user_account.ua_last_name) as customer_name,
                            product.prod_name, product_variant.var_capacity')
                    ->join('product_booking', 'product_assignment.pa_order_id', 'product_booking.pb_id')
                    ->join('product_variant', 'product_booking.pb_variant_id', 'product_variant.var_id')
                    ->join('product', 'product_variant.prod_id', 'product.prod_id')
                    ->join('customer', 'product_booking.pb_customer_id', 'customer.cu_account_id')
                    ->join('user_account', 'customer.cu_account_id', 'user_account.ua_id')
                    ->where('product_assignment.pa_technician_id = :technician_id')
                    ->bind(['technician_id' => $technicianId])
                    ->orderBy('product_booking.pb_preferred_date DESC, product_booking.pb_preferred_time DESC')
                    ->get();
    }

    /**
     * Get all assignments for a specific product booking
     * 
     * @param int $bookingId The product booking ID
     * @return array The assignments for the product booking
     */
    public function getAssignmentsByBooking($bookingId)
    {
        return $this->select('product_assignment.*, 
                            CONCAT(user_account.ua_first_name, \' \', user_account.ua_last_name) as technician_name')
                    ->join('technician', 'product_assignment.pa_technician_id', 'technician.te_account_id')
                    ->join('user_account', 'technician.te_account_id', 'user_account.ua_id')
                    ->where('product_assignment.pa_order_id = :booking_id')
                    ->bind(['booking_id' => $bookingId])
                    ->get();
    }

    /**
     * Create a new product assignment
     * 
     * @param array $data The assignment data
     * @return int|bool The new assignment ID or false on failure
     */
    public function createAssignment($data)
    {
        return $this->insert($data);
    }

    /**
     * Update a product assignment
     * 
     * @param int $id The assignment ID
     * @param array $data The assignment data
     * @return bool Success or failure
     */
    public function updateAssignment($id, $data)
    {
        return $this->update($data, 'pa_id = :id', ['id' => $id]);
    }

    /**
     * Delete a product assignment
     * 
     * @param int $id The assignment ID
     * @return bool Success or failure
     */
    public function deleteAssignment($id)
    {
        return $this->delete('pa_id = :id', ['id' => $id]);
    }
} 
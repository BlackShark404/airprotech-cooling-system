<?php

namespace App\Models;

class ProductBookingModel extends Model
{
    protected $table = 'PRODUCT_BOOKING';

    /**
     * Get all product bookings
     */
    public function getAllBookings()
    {
        $sql = "SELECT 
                    pb.*,
                    ua.UA_FIRST_NAME || ' ' || ua.UA_LAST_NAME AS CUSTOMER_NAME,
                    pv.VAR_CAPACITY,
                    p.PROD_NAME,
                    p.PROD_IMAGE
                FROM {$this->table} pb
                JOIN CUSTOMER c ON pb.PB_CUSTOMER_ID = c.CU_ACCOUNT_ID
                JOIN USER_ACCOUNT ua ON c.CU_ACCOUNT_ID = ua.UA_ID
                JOIN PRODUCT_VARIANT pv ON pb.PB_VARIANT_ID = pv.VAR_ID
                JOIN PRODUCT p ON pv.PROD_ID = p.PROD_ID
                WHERE pb.PB_DELETED_AT IS NULL
                ORDER BY pb.PB_ORDER_DATE DESC";
        
        return $this->query($sql);
    }

    /**
     * Get a specific product booking by ID
     */
    public function getBookingById($bookingId)
    {
        $sql = "SELECT 
                    pb.*,
                    ua.UA_FIRST_NAME || ' ' || ua.UA_LAST_NAME AS CUSTOMER_NAME,
                    ua.UA_EMAIL AS CUSTOMER_EMAIL,
                    ua.UA_PHONE_NUMBER AS CUSTOMER_PHONE,
                    pv.VAR_CAPACITY,
                    p.PROD_NAME,
                    p.PROD_IMAGE
                FROM {$this->table} pb
                JOIN CUSTOMER c ON pb.PB_CUSTOMER_ID = c.CU_ACCOUNT_ID
                JOIN USER_ACCOUNT ua ON c.CU_ACCOUNT_ID = ua.UA_ID
                JOIN PRODUCT_VARIANT pv ON pb.PB_VARIANT_ID = pv.VAR_ID
                JOIN PRODUCT p ON pv.PROD_ID = p.PROD_ID
                WHERE pb.PB_ID = :booking_id AND pb.PB_DELETED_AT IS NULL";
        
        return $this->queryOne($sql, [':booking_id' => $bookingId]);
    }

    /**
     * Get all bookings for a specific customer
     */
    public function getBookingsByCustomerId($customerId)
    {
        $sql = "SELECT 
                    pb.*,
                    pv.VAR_CAPACITY,
                    p.PROD_NAME,
                    p.PROD_IMAGE
                FROM {$this->table} pb
                JOIN PRODUCT_VARIANT pv ON pb.PB_VARIANT_ID = pv.VAR_ID
                JOIN PRODUCT p ON pv.PROD_ID = p.PROD_ID
                WHERE pb.PB_CUSTOMER_ID = :customer_id AND pb.PB_DELETED_AT IS NULL
                ORDER BY pb.PB_ORDER_DATE DESC";
        
        return $this->query($sql, [':customer_id' => $customerId]);
    }

    /**
     * Create a new product booking
     */
    public function createBooking($data)
    {
        $sql = "INSERT INTO {$this->table} (
                    PB_CUSTOMER_ID, 
                    PB_VARIANT_ID, 
                    PB_QUANTITY, 
                    PB_UNIT_PRICE, 
                    PB_STATUS, 
                    PB_PREFERRED_DATE, 
                    PB_PREFERRED_TIME, 
                    PB_ADDRESS
                ) VALUES (
                    :customer_id, 
                    :variant_id, 
                    :quantity, 
                    :unit_price, 
                    :status, 
                    :preferred_date, 
                    :preferred_time, 
                    :address
                )";
        
        $params = [
            ':customer_id' => $data['PB_CUSTOMER_ID'],
            ':variant_id' => $data['PB_VARIANT_ID'],
            ':quantity' => $data['PB_QUANTITY'],
            ':unit_price' => $data['PB_UNIT_PRICE'],
            ':status' => $data['PB_STATUS'] ?? 'pending',
            ':preferred_date' => $data['PB_PREFERRED_DATE'],
            ':preferred_time' => $data['PB_PREFERRED_TIME'],
            ':address' => $data['PB_ADDRESS']
        ];
        
        $this->execute($sql, $params);
        return $this->lastInsertId('product_booking_pb_id_seq');
    }

    /**
     * Update a product booking status
     */
    public function updateBookingStatus($bookingId, $status)
    {
        $sql = "UPDATE {$this->table} SET 
                PB_STATUS = :status,
                PB_UPDATED_AT = CURRENT_TIMESTAMP
                WHERE PB_ID = :booking_id AND PB_DELETED_AT IS NULL";
        
        $params = [
            ':status' => $status,
            ':booking_id' => $bookingId
        ];
        
        return $this->execute($sql, $params);
    }

    /**
     * Update an existing product booking
     */
    public function updateBooking($bookingId, $data)
    {
        $setClauses = [];
        $params = [':booking_id' => $bookingId];

        if (isset($data['PB_QUANTITY'])) {
            $setClauses[] = "PB_QUANTITY = :quantity";
            $params[':quantity'] = $data['PB_QUANTITY'];
        }
        
        if (isset($data['PB_UNIT_PRICE'])) {
            $setClauses[] = "PB_UNIT_PRICE = :unit_price";
            $params[':unit_price'] = $data['PB_UNIT_PRICE'];
        }
        
        if (isset($data['PB_STATUS'])) {
            $setClauses[] = "PB_STATUS = :status";
            $params[':status'] = $data['PB_STATUS'];
        }
        
        if (isset($data['PB_PREFERRED_DATE'])) {
            $setClauses[] = "PB_PREFERRED_DATE = :preferred_date";
            $params[':preferred_date'] = $data['PB_PREFERRED_DATE'];
        }
        
        if (isset($data['PB_PREFERRED_TIME'])) {
            $setClauses[] = "PB_PREFERRED_TIME = :preferred_time";
            $params[':preferred_time'] = $data['PB_PREFERRED_TIME'];
        }
        
        if (isset($data['PB_ADDRESS'])) {
            $setClauses[] = "PB_ADDRESS = :address";
            $params[':address'] = $data['PB_ADDRESS'];
        }

        if (empty($setClauses)) {
            return false; // No fields to update
        }

        $setClauses[] = "PB_UPDATED_AT = CURRENT_TIMESTAMP";
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . 
               " WHERE PB_ID = :booking_id AND PB_DELETED_AT IS NULL";
        
        return $this->execute($sql, $params);
    }

    /**
     * Soft delete a product booking
     */
    public function deleteBooking($bookingId)
    {
        $sql = "UPDATE {$this->table} SET PB_DELETED_AT = CURRENT_TIMESTAMP 
                WHERE PB_ID = :booking_id";
        return $this->execute($sql, [':booking_id' => $bookingId]);
    }

    /**
     * Get booking summary statistics
     */
    public function getBookingSummary()
    {
        $sql = "SELECT 
                    COUNT(*) AS TOTAL_BOOKINGS,
                    COUNT(CASE WHEN PB_STATUS = 'pending' THEN 1 END) AS PENDING_BOOKINGS,
                    COUNT(CASE WHEN PB_STATUS = 'confirmed' THEN 1 END) AS CONFIRMED_BOOKINGS,
                    COUNT(CASE WHEN PB_STATUS = 'completed' THEN 1 END) AS COMPLETED_BOOKINGS,
                    COUNT(CASE WHEN PB_STATUS = 'cancelled' THEN 1 END) AS CANCELLED_BOOKINGS,
                    SUM(PB_QUANTITY * PB_UNIT_PRICE) AS TOTAL_REVENUE
                FROM {$this->table}
                WHERE PB_DELETED_AT IS NULL";
        
        return $this->queryOne($sql);
    }
} 
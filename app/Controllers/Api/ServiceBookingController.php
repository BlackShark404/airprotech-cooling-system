<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Database;
use Core\Session;

class ServiceBookingController extends Controller {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all service bookings for the current user
     */
    public function getUserServiceBookings() {
        // Get the current user ID from the session
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->sendJsonResponse(['error' => 'User not authenticated'], 401);
            return;
        }

        // Query to get all service bookings for the user with service type information
        $query = "SELECT sb.*, st.ST_NAME, st.ST_DESCRIPTION, st.ST_CODE 
                 FROM service_bookings sb
                 JOIN service_types st ON sb.ST_ID = st.ST_ID
                 WHERE sb.USER_ID = ?
                 ORDER BY sb.SB_CREATED_AT DESC";
        
        $serviceBookings = $this->db->query($query, [$userId])->fetchAll();
        
        $this->sendJsonResponse($serviceBookings);
    }

    /**
     * Get a specific service booking by ID
     */
    public function getServiceBooking($id) {
        // Get the current user ID from the session
        $userId = Session::get('user_id');
        
        if (!$userId) {
            $this->sendJsonResponse(['error' => 'User not authenticated'], 401);
            return;
        }

        // Query to get the specific service booking with service type information
        $query = "SELECT sb.*, st.ST_NAME, st.ST_DESCRIPTION, st.ST_CODE 
                 FROM service_bookings sb
                 JOIN service_types st ON sb.ST_ID = st.ST_ID
                 WHERE sb.SB_ID = ? AND sb.USER_ID = ?";
        
        $serviceBooking = $this->db->query($query, [$id, $userId])->fetch();
        
        if (!$serviceBooking) {
            $this->sendJsonResponse(['error' => 'Service booking not found'], 404);
            return;
        }
        
        $this->sendJsonResponse($serviceBooking);
    }

    /**
     * Helper method to send JSON response
     */
    private function sendJsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
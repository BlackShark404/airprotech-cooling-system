-- Insert test data for service request management

-- Ensure user roles exist
INSERT INTO user_role (ur_name) 
VALUES ('customer'), ('technician'), ('admin') 
ON CONFLICT (ur_name) DO NOTHING;

-- Set role IDs based on database
DO $$
DECLARE 
    customer_role_id INT;
    technician_role_id INT;
    admin_role_id INT;
BEGIN
    SELECT ur_id INTO customer_role_id FROM user_role WHERE ur_name = 'customer';
    SELECT ur_id INTO technician_role_id FROM user_role WHERE ur_name = 'technician';
    SELECT ur_id INTO admin_role_id FROM user_role WHERE ur_name = 'admin';

    -- Insert test users
    INSERT INTO user_account 
        (ua_first_name, ua_last_name, ua_email, ua_hashed_password, ua_phone_number, ua_role_id, ua_is_active)
    VALUES 
        ('Test', 'Customer', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', customer_role_id, true),
        ('John', 'Technician', 'tech1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', technician_role_id, true),
        ('Jane', 'Technician', 'tech2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5555555555', technician_role_id, true)
    ON CONFLICT (ua_email) DO NOTHING;
END $$;

-- Create service booking for testing
INSERT INTO service_booking 
    (sb_customer_id, sb_service_type_id, sb_preferred_date, sb_preferred_time, 
     sb_address, sb_description, sb_status, sb_priority, sb_estimated_cost)
VALUES
    ((SELECT ua_id FROM user_account WHERE ua_email = 'customer@example.com'), 
     (SELECT st_id FROM service_type WHERE st_code = 'installation'), 
     CURRENT_DATE + INTERVAL '3 days', '10:00:00',
     '123 Test Street, Test City, Test Country',
     'Need installation of a new AC unit in the living room.',
     'pending', 'moderate', 250.00); 
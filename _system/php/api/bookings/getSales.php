<?php
# Set database parameters
$servername = "localhost";  
$username = "root";
$password = "mysql";

#Retrieve POST parameters
$booking_created = $_POST['booking_created'];

if (isset($date));
{
    try
    {
        # Connect to Database
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        # Perform SQL Query
        $sql = "SELECT booking_date, booking_id, booking_total_price FROM bookings WHERE booking_created = '$booking_created' ORDER BY booking_date ASC, booking_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        # Fetch Result
        $result = $stmt->fetchAll();
    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed: " . $e->getMessage()
        ]);
    }
}
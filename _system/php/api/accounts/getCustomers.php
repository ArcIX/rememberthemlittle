<?php
# Set database parameters
$servername = "localhost";  
$username = "root";
$password = "mysql";

#Retrieve POST parameters

if (true);
{
    try
    {
        # Connect to Database
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        # Perform SQL Query
        # Desired output: Account Name | Number of Bookings Made Since The Beginning | Total Cash Paid To RTL | Latest Pictorial Session | Next Pictorial Session
        $sql = "SELECT a.account_name, COUNT(b.account_id) AS number_of_bookings, SUM(b.booking_total_price) AS total_expediture FROM accounts a LEFT JOIN bookings b ON a.account_id = b.account_id GROUP BY a.account_id";
        
        # Get latest booking of each customer
        # $sql = "SELECT MAX(b.booking_date) FROM accounts a LEFT JOIN bookings b WHERE b.booking_date < GETDATE() GROUP BY a.account_id"
        # Get next booking of each customer
        # $sql = "SELECT MIN(b.booking_date) FROM accounts a LEFT JOIN bookings b WHERE b.booking_date > GETDATE() GROUP BY a.account_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        # Fetch Result
        $result = $stmt->fetchAll();

        print_r($result);
    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed: " . $e->getMessage()
        ]);
    }
}
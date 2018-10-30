<?php
# Set database parameters
$servername = "localhost";  
$username = "root";
$password = "root";
#Retrieve POST parameters
$date = $_POST['booking_date'];
$duration = $_POST['package_minutes'];

if (isset($date) && isset($duration))
{

    # Convert to PHP recognized format
    $duration = "+$duration minutes";

    try
    {
        # Connect to Database
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        # Perform SQL Query
        $sql = "SELECT b.booking_id, b.booking_date, b.booking_time, p.package_minutes FROM bookings b INNER JOIN packages p ON b.booking_date = '$date' AND b.package_id = p.package_id ORDER BY b.booking_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        # Fetch Result
        $result = $stmt->fetchAll();
        
        // print_r($result);
        $start = strtotime("8:00"); #opening time
        $stop = strtotime("20:00"); #closing time
        $step = "+30 minutes"; #preferred interval
        $reserved = $result; 
        
        $availables = array(); //open slots to be pushed here
        
        $c = 0;
        $current = $start; #initialize current time
        $end_of_current = strtotime($duration, $current); #initialize and compute end time of current
        $reserved_start = strtotime($reserved[$c]['booking_time']);
        $reserved_duration = "+".$reserved[$c]['package_minutes']." minutes";
        $end_of_reserved = strtotime($reserved_duration, $reserved_start);
        
        while ($current < $stop && strtotime($duration, $current) <= $stop)
        {   
            if($current >= $reserved_start && $end_of_current <= $end_of_reserved)
            {
               //echo "<br>Conflict with booking #".$reserved[$c]['booking_id']." @".date("H:i", $reserved_start)." - ".date("H:i", $end_of_reserved)."<br>"; 
            }
            else
            {
                $availables[] = date("H:i", $current);
                //echo "<br>".date("H:i", $current)."<br>";
            }
            
            if($current >= $end_of_reserved && $c < sizeof($reserved) - 1)
            {
                //echo "<br>Past booking #".$reserved[$c]['booking_id']." @".date("H:i", $reserved_start)." - ".date("H:i", $end_of_reserved)."<br>";
                $c = $c + 1;
                $reserved_start = strtotime($reserved[$c]['booking_time']);
                $reserved_duration = "+".$reserved[$c]['package_minutes']." minutes";
                $end_of_reserved = strtotime($reserved_duration, $reserved_start);
            }
            $current = strtotime($step, $current);
            $end_of_current = strtotime($duration, $current);
        }

        echo json_encode((object)[
            'success' => true,
            'data' => $availables
        ]);

    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed"
        ]);
    }
}
else{
    echo json_encode((object)[
        'success' => false,
        'message' => "Error"
    ]);
}
?>
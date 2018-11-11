<?php
# Set database parameters
$servername = "localhost";  
$username = "root";
$password = "mysql";

#Retrieve POST parameters
$booking_date = "2018-10-20"; #$_POST['booking_date'];

if (isset($booking_date))
{

    # Convert to PHP recognized format
    $duration = "+$duration minutes";

    try
    {
        # Connect to Database
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        # Perform SQL Query
        $sql = "SELECT booking_id, booking_date, booking_time, package FROM bookings b WHERE booking_date = '$booking_date' ORDER BY booking_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        # Fetch Result
        $result = $stmt->fetchAll();

        //print_r($result);

        echo "<table border=2>";

        $now = strtotime("8:00");
        $stop = strtotime("20:00");
        $step = "+30 minutes";
        $rowspan = 1;

        for($i = 0, $reserved_start = strtotime($result[$i]['booking_time']), $reserved_duration = json_decode($result[$i]['package'])->package_minutes, $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start); $now < $stop; $now = strtotime($step, $now))
        {
            echo "<tr>";
            
            echo "<td>".date("H:i", $now)."</td>";
            //echo date("H:i", $now)." >> ".$result[$i]['booking_time']." - ".date("H:i", $reserved_finish)."<br>";
            /*
            if($now >= $reserved_finish)
            {
                $i = $i + 1;
                $reserved_start = strtotime($result[$i]['booking_time']);
                $reserved_duration = json_decode($result[$i]['package'])->package_minutes;
                $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start);
            }
            */
            
            if($now == $reserved_start)
            {
                $rowspan = $reserved_duration / 30;
                echo "<td rowspan=".$rowspan.">".$result[$i]['booking_id']."</td>";

                $i = $i + 1;
                $reserved_start = strtotime($result[$i]['booking_time']);
                $reserved_duration = json_decode($result[$i]['package'])->package_minutes;
                $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start);
            }
            else if($rowspan == 1) //$now < $reserved_start || $now >= $reserved_finish
            {
                echo "<td>Open</td>";
            }
            else 
            {
                $rowspan = $rowspan - 1;
            }

            echo "</tr>";
        }

        echo "</table>";
    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed. ".$e->getMessage()
        ]);
    }
}
else
{
    echo json_encode((object)[
        'success' => false,
        'message' => "Error"
    ]);
}  
?>
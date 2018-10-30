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
        $sql = "SELECT b.booking_id, b.booking_date, b.booking_time, p.package_minutes FROM bookings b INNER JOIN packages p ON b.booking_date = '$booking_date' AND b.package_id = p.package_id ORDER BY b.booking_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        # Fetch Result
        $result = $stmt->fetchAll();

        print_r($result);

        echo "<table border=2>";

        for($i = 0, $now = strtotime("8:00"), $stop = strtotime("20:00"), $step = "+30 minutes", $reserved_start = strtotime($result[$i]['booking_time']), $reserved_duration = "+".$result[$i]['package_minutes']." minutes", $reserved_finish = strtotime($reserved_duration, $reserved_start); $now < $stop; $now = strtotime($step, $now))
        {
            echo "<tr>";
            
            echo "<td>".date("H:i", $now)."</td>";
            
            if($now == $reserved_start)
            {
                $rowspan = $result[$i]['package_minutes'] / 30;
                echo "<td rowspan=".$rowspan.">".$result[$i]['booking_id']."</td>";
            }
            else if($now >= $reserved_start && $now < $reserved_finish)
            {
                   
            }
            else if($now == $reserved_finish)
            {
                echo "<td>Open</td>";
                $i = $i + 1;
                $reserved_start = strtotime($result[$i]['booking_time']);
                $reserved_duration = "+".$result[$i]['package_minutes']." minutes";
                $reserved_finish = strtotime($reserved_duration, $reserved_start);
            }
            else
            {
                echo "<td>Open</td>";
            }

            echo "</tr>";
        }

        echo "$i</table>";
    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed"
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
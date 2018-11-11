<?php
# Set database parameters
$servername = "localhost";  
$username = "root";
$password = "mysql";

#Retrieve POST parameters
$from_date = "2018-10-20"; #$_POST['from_date'];

if (isset($from_date))
{
    try
    {
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //echo $booking_date."<br>";
        $upto_date = date("Y-m-d", strtotime("+6 days", strtotime($from_date)));
        //echo $upto_date;

        # Perform SQL Query
        $sql = "SELECT booking_id, booking_date, booking_time, package FROM bookings WHERE booking_date BETWEEN '$from_date' AND '$upto_date' ORDER BY booking_time ASC, booking_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        # Fetch Result
        $result = $stmt->fetchAll();

        print_r($result);

        echo "<table border=2>";

        $now = strtotime("8:00");
        $stop = strtotime("20:00");
        $step = "+30 minutes";
        $upto_date = strtotime($upto_date);
        $rowspans = array(
            1, 1, 1, 1, 1, 1, 1
        );

        echo "<tr>";
        echo "<td></td>";
        for($day = strtotime($from_date); $day <= $upto_date; $day = strtotime("+1 day", $day))
        {
            echo "<td>".date("D m/d/Y", $day)."</td>";
        }
        echo "</tr>";
        
        for($i = 0, $reserved_start = strtotime($result[$i]['booking_time']), $reserved_duration = json_decode($result[$i]['package'])->package_minutes, $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start), $reserved_date = strtotime($result[$i]['booking_date']); $now < $stop; $now = strtotime($step, $now))
        {
            echo "<tr>";
            
            echo "<td>".date("H:i", $now)."</td>";

            for($j = 0, $day = strtotime($from_date); $day <= $upto_date; $day = strtotime("+1 day", $day), $j++)
            {
                //echo date("H:i", $now)." == ".date("H:i", $reserved_start)." && ".date("m/d/Y", $day)." == ".date("m/d/Y", $reserved_date)." <br>";

                if($now == $reserved_start && $day == $reserved_date)
                {
                    $rowspan = $reserved_duration / 30;
                    $rowspans[$j] = $rowspan;
                    echo "<td rowspan=".$rowspan.">".$result[$i]['booking_id']."</td>";

                    $i = $i + 1;
                    $reserved_start = strtotime($result[$i]['booking_time']);
                    $reserved_duration = json_decode($result[$i]['package'])->package_minutes;
                    $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start);
                    $reserved_date = strtotime($result[$i]['booking_date']);
                }
                else if($rowspans[$j] == 1)
                {
                    echo "<td>Open</td>";
                }
                else
                {
                    $rowspans[$j] = $rowspans[$j] - 1;
                }
            }

            echo "</tr>";
        }

        echo "</table>";
    }
    catch(PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed" . $e->getMessage()
        ]);
    }
}

/*
if($now >= $reserved_finish && $day > $reserved_date)
                {
                    $i = $i + 1;
                    $reserved_start = strtotime($result[$i]['booking_time']);
                    $reserved_duration = json_decode($result[$i]['package'])->package_minutes;
                    $reserved_finish = strtotime("+".$reserved_duration." minutes", $reserved_start);
                    $reserved_date = strtotime($result[$i]['booking_date']);
                }

                if($now == $reserved_start && $day == $reserved_date)
                {
                    $rowspan = $reserved_duration / 30;
                    echo "<td rowspan=".$rowspan.">".$result[$i]['booking_id']."</td>";
                }
                else if($now < $reserved_start || $now >= $reserved_finish || $day <> $reserved_date)
                {
                    echo "<td>Open</td>";
                }
*/
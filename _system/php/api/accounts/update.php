<?php
# Set database parameters
$servername = "localhost";
$username = "root";
$password = "mysql";

# Retrieve POST parameters
$account_id = $_POST['account_id'];
$account_type = $_POST['account_type'];
$account_name = $_POST['account_name'];
$account_fb = $_POST['account_fb'];
$account_contact = $_POST['account_contact'];
$account_email = $_POST['account_email'];
$account_password = $_POST['account_password'];

# Check parameters if null
if (isset($account_id) && isset($account_type) && isset($account_name) && isset($account_fb) && isset($account_contact) && isset($account_email) && isset($account_password))
{
    try
    {
        # Connect to Database
        $conn = new PDO("mysql:host=$servername;dbname=rtl_v1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        # Perform SQL Query
        $sql = "UPDATE accounts SET account_type='$account_type', account_name='$account_name', account_fb = '$account_fb', account_contact='$account_contact', account_email='$account_email', account_password='$account_password' WHERE account_id='$account_id'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        # Print success
        echo json_encode((object)[
            'success' => true
        ]);
    }
    catch (PDOException $e)
    {
        echo json_encode((object)[
            'success' => false,
            'message' => "Connection failed: " . $e->getMessage()
        ]);
    }
}
else
{
    echo 'values not set';
}
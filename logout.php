<?php
session_start();
include('connect.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
echo("Employee ID: " . $employee_id . "<br>"); 

try {
    $start_time = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : time();
    $current_time = time();
    $elapsed_time = $current_time - $start_time;
    $query = "UPDATE employees 
              SET working_hours = working_hours + ?, 
                  last_logout = CURRENT_TIMESTAMP 
              WHERE employee_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $elapsed_time, $employee_id);
    
    $debug_params = "Elapsed Time: " . $elapsed_time . " seconds; Employee ID: " . $employee_id;
    echo("Debug Parameters: " . $debug_params . "<br>");
    
    $stmt->execute();
    
    if ($stmt->affected_rows != 1) {
        throw new Exception("Database update affected " . $stmt->affected_rows . " rows instead of 1.");
    }

    $stmt->close();
    mysqli_close($conn);

    session_unset();
    session_destroy();

    header("Location: login.php?status=loggedout");
    exit;

} catch (Exception $e) {

    echo "An error occurred: " . $e->getMessage() . "<br>";
    echo "SQL Query: " . $query . "<br>";
    echo "Parameters: " . $debug_params . "<br>";
    mysqli_close($conn);
  
    session_unset();
    session_destroy();

    exit;
}
?>
<?php
// Start the session
session_start();

// Check if the employee is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Retrieve employee details from session
$employee_name = $_SESSION['employee_name'];
$employee_position = $_SESSION['employee_position'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <!-- Link to Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Employee Details</h2>

        <div class="mb-4">
            <p class="text-lg font-medium text-gray-700"><strong>Name:</strong> <?php echo $employee_name; ?></p>
            <p class="text-lg font-medium text-gray-700"><strong>Position:</strong> <?php echo $employee_position; ?></p>
        </div>

        <a href="logout.php" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 text-center">Logout</a>
    </div>

</body>
</html>
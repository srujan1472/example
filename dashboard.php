<?php
session_start();
if (!isset($_SESSION['employee_id']) || !isset($_SESSION['employee_name'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

include('connect.php');

$employee_id = $_SESSION['employee_id'];
$employee_name = $_SESSION['employee_name'];

$query = "SELECT * FROM employees WHERE employee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee_details = $result->fetch_assoc();
    $total_hours = $employee_details['working_hours'] / 3600;
    $total_hours = round($total_hours, 2);
    $formatted_hours = number_format($total_hours, 2);
} else {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@heroicons/v2.0.18/24/outline/index.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen dashboard">
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xl font-bold text-gray-800">Employee Dashboard</span>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Current Session</h2>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div id="time-display" class="flex-1">
                        <p class="text-xl font-semibold text-gray-700">Loading time...</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Personal Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Employee ID:</span> <?php echo $employee_details['employee_id']; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6V4a1 1 0 00-1-1H4a1 1 0 00-1 1v2m4 0V8a2 2 0 012-2h6a2 2 0 012 2v2M6 8V4a1 1 0 011-1h2a1 1 0 011 1v4m-6 0H4a2 2 0 00-2 2v8a2 2 0 002 2h16a2 2 0 002-2v-8a2 2 0 00-2-2h-2m4-4h4m-4-4v4m-6 0h6"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Name:</span> <?php echo $employee_details['full_name']; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.5 7.5 7.5-7.5M3 15l7.5 7.5 7.5-7.5"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Email:</span> <?php echo $employee_details['email']; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Date of Birth:</span> <?php echo date('M d, Y', strtotime($employee_details['date_of_birth'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Work Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Current Project:</span> <?php echo $employee_details['current_project']; ?>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Total Working Hours:</span> <?php echo $formatted_hours; ?> hours
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-700">
                                <span class="font-medium">Last Logout Time:</span> <?php echo $employee_details['last_logout']; ?>
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center mt-6">
    <a href="logout.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg shadow-md hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition duration-300 transform hover:-translate-y-1">
      <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
      </svg>
      Sign Out
    </a>
  </div>
        </main>
    </div>

    <script>
        function updateTimer() {
            const startTime = <?php echo $_SESSION['start_time']; ?>;
            const currentTime = new Date();
            const diff = currentTime - new Date(startTime * 1000);

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('time-display').innerHTML = `
                <p class="text-xl font-semibold text-gray-700">
                    Active Time: ${hours} hours ${minutes} minutes ${seconds} seconds
                </p>
            `;
        }
        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>
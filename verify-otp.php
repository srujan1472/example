<?php
session_start();
include('connect.php');

if (!empty($_POST['otp'])) {
    $otp = $_POST['otp'];
    $employeeId = $_SESSION['employee_id'];
    
    $query = "SELECT * FROM employees_temp WHERE employee_id = '$employeeId' AND OTP = '$otp'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, move data to main table
        $row = mysqli_fetch_assoc($result);
        
        $query = "INSERT INTO employees (
            employee_id,
            full_name,
            email,
            date_of_birth,
            current_project,
            working_hours,
            created_at
        ) VALUES (
            '{$row['employee_id']}',
            '{$row['full_name']}',
            '{$row['email']}',
            '{$row['date_of_birth']}', 
            '{$row['current_project']}', 
            '0',
            '{$row['created_at']}'
        )";
        
        if (mysqli_query($conn, $query)) {
            // Delete from temp table
            mysqli_query($conn, "DELETE FROM employees_temp WHERE employee_id = '$employeeId'");
            
            $success = "Registration successful! Your Employee ID is: {$row['employee_id']}";
            session_unset();
        } else {
            $error = "Error registering employee: " . mysqli_error($conn);
        }
    } else {
        $error = "Invalid OTP";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen">
    <div class="container mx-auto px-4 h-full">
        <div class="flex items-center justify-center h-full">
            <div class="w-full max-w-md">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Verify Your Account</h1>
                    
                    <?php if (!empty($success)) : ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)) : ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="otp">
                                Enter OTP
                            </label>
                            <input type="number" id="otp" name="otp" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">
                            Verify
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
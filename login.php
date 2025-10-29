<?php
session_start();

include('connect.php');

$employee_details = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = trim($_POST['employee_id']);

    if (!empty($employee_id)) {
        try {
            $query = "SELECT * FROM employees WHERE employee_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $employee_details = $result->fetch_assoc();
                if (!isset($_SESSION)) {
                    session_start();
                }

                $_SESSION['employee_id'] = $employee_details['employee_id'];
                $_SESSION['employee_name'] = $employee_details['full_name']; 
                $_SESSION['login_time'] = time(); 
                echo "<script>
                      alert('Logged in successfully!');
                       window.location.href = 'dashboard.php';
                      </script>";
                exit();
            } else {
                $error_message = "Employee ID not found.";
            }
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter your Employee ID.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="w-full max-w-sm p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Employee Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="text-red-500 text-sm text-center mb-4"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-4">
            <div>
                <label for="employee_id" class="block text-gray-700 font-medium">Employee ID</label>
                <input type="text" id="employee_id" name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-300">
                    Login
                </button>
            </div>
            <p class="text-center mt-4">
            <a href="register.php" class="text-blue-500 hover:text-blue-700">Register Now</a>
            </p>
        </form>
    </div>

</body>
</html>
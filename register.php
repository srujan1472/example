<?php
    include('connect.php');
    $fullName = $email = $dob = $employeeId = $currentProject = $workingHours = "";
    $error = "";
    $success = "";

    // Generate Employee ID
    $currentYear = date('Y');
    $randomNumber = rand(100, 999);
    $employeeId = $currentYear . '-' . str_pad($randomNumber, 3, '0', STR_PAD_LEFT);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $currentProject = $_POST['currentProject'];

        if (empty($fullName) || empty($email) || empty($dob) || empty($currentProject)) {
            $error = "All fields are required!";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format!";
            } else {
                // Check if email already exists
                $query = "SELECT email FROM employees WHERE email = '$email'";
                $result = mysqli_query($conn, $query);
                
                if (mysqli_num_rows($result) > 0) {
                    $error = "Email already exists!";
                } else {
                    // Generate OTP
                    $OTP = mt_rand(100000, 999999);
                    
                    // Create temporary data
                    $regFullName = $fullName;
                    $regEmail = $email;
                    $regDob = $dob;
                    $regCurrentProject = $currentProject;
                    
                    // Store temporary data in database
                    $query = "INSERT INTO employees_temp (
                        employee_id,
                        full_name,
                        email,
                        date_of_birth,
                        current_project,
                        OTP,
                        created_at
                    ) VALUES (
                        '$employeeId',
                        '$fullName',
                        '$email',
                        '$dob',
                        '$currentProject',
                        '$OTP',
                        CURRENT_TIMESTAMP
                    )";
                    
                    if (mysqli_query($conn, $query)) {
                        // Send OTP to email
                        $subject = "Verify your email address";
                        $message = "Your OTP for registration is: $OTP";
                        $headers = "From: your-verification-system@example.com" . "\r\n" .
                            "CC: somebodyelse@example.com";

                        if (mail($email, $subject, $message, $headers)) {
                            $success = "OTP has been sent to your email address. Please verify your account.";
                            session_start();
                            $_SESSION['employee_id'] = $employeeId;
                            $_SESSION['OTP'] = $OTP;
                        } else {
                            $error = "Failed to send OTP. Please try again.";
                        }
                    } else {
                        $error = "Error registering employee: " . mysqli_error($conn);
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen">
    <div class="container mx-auto px-4 h-full">
        <div class="flex items-center justify-center h-full">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Employee Registration</h1>
                    <p class="text-gray-600">Please fill in your details below</p>
                </div>

                <?php if (!empty($success)) : ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $success; ?>
                        <form action="verify-otp.php" method="POST" class="mt-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="otp">
                                    Enter OTP
                                </label>
                                <input type="number" name="otp" id="otp" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">
                                Verify OTP
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="fullName">
                            Full Name
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               id="fullName" type="text" name="fullName" placeholder="Name"
                               value="<?php echo $fullName; ?>">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               id="email" type="email" name="email" placeholder="abc@gmail.com"
                               value="<?php echo $email; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="dob">
                            Date of Birth
                        </label>
                        <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               id="dob" type="date" name="dob"
                               value="<?php echo $dob; ?>">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="currentProject">
                            Current Project
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                id="currentProject" name="currentProject">
                            <option value="">Select a project</option>
                            <option value="Project Alpha">Project Alpha</option>
                            <option value="Project Beta">Project Beta</option>
                            <option value="Project Gamma">Project Gamma</option>
                            <option value="Project Delta">Project Delta</option>
                            <option value="Project Echo">Project Echo</option>
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">
                        Register
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
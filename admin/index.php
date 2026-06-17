<?php
session_start();
include("../db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT admin_id, username, password 
        FROM admin 
        WHERE username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin["password"])) {

            $_SESSION["admin_id"] = $admin["admin_id"];
            $_SESSION["admin_name"] = $admin["username"];

            $stmt->close(); // Close here right before redirecting
            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password";
    $stmt->close(); // Close here if authentication fails
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg w-full max-w-md transition-all duration-300">

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">
            Admin Login
        </h2>

        <?php if ($error) { ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-lg mb-4 text-sm font-medium">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    required
                    autocomplete="username"
                    class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 text-sm sm:text-base">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 text-sm sm:text-base">
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white font-semibold p-3 rounded-lg hover:bg-blue-700 active:scale-[0.99] transition transform text-sm sm:text-base shadow-md">
                Login
            </button>
        </form>

    </div>

</body>

</html>
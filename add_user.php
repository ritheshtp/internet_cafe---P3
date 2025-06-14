<?php
require 'db_connect.php';

$errors = [];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name          = trim($_POST['name']);
    $address       = trim($_POST['address']);
    $mobile_number = trim($_POST['mobile_number']);
    $email         = trim($_POST['email']);

    if (!preg_match("/^[A-Z][a-z]*( [A-Z][a-z]*)*$/", $name)) {
        $errors[] = "Name must start with capital letters and contain only alphabets.";
    }

    if (!preg_match("/^[6-9][0-9]{9}$/", $mobile_number)) {
        $errors[] = "Mobile number must start with 6, 7, 8, or 9 and be 10 digits.";
    } else {
        $checkMobile = $conn->prepare("SELECT id FROM user WHERE mobile_number = ?");
        $checkMobile->bind_param("s", $mobile_number);
        $checkMobile->execute();
        $checkMobile->store_result();
        if ($checkMobile->num_rows > 0) {
            $errors[] = "Mobile number already exists.";
        }
        $checkMobile->close();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
        $errors[] = "Email must be a valid Gmail address (e.g., yourname@gmail.com).";
    } else {
        $checkEmail = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();
        if ($checkEmail->num_rows > 0) {
            $errors[] = "Email already exists.";
        }
        $checkEmail->close();
    }

    if (strlen($address) < 10) {
        $errors[] = "Address must be at least 10 characters long.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO user (name, address, mobile_number, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $address, $mobile_number, $email);

        if ($stmt->execute()) {
            $message = "User added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fbc2eb, #a6c1ee 100%);
            margin: 0; padding: 0;
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 12px;
            transition: color 0.3s ease;
        }
        .navbar a:hover { color: #007bff; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 170px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            z-index: 999;
            border-radius: 6px;
        }
        .dropdown-content a {
            color: #333;
            padding: 10px 14px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f0f0f0;
            color: #007bff;
        }
        .dropdown:hover .dropdown-content { display: block; }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            padding: 30px;
        }
        .form-container {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: bold;
            letter-spacing: 1px;
        }
        form input[type="text"],
        form input[type="email"],
        form textarea {
            width: 100%;
            padding: 12px 15px;
            margin-top: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }
        form input:focus,
        form textarea:focus {
            border-color: #74ebd5;
            outline: none;
            box-shadow: 0 0 8px rgba(116, 235, 213, 0.5);
        }
        button {
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            border: none;
            padding: 14px;
            border-radius: 8px;
            width: 100%;
            color: white;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background: linear-gradient(to right, #ACB6E5, #74ebd5);
        }
        .field-error {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }
        p {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="dropdown">
        <a href="#"><i class="fas fa-desktop"></i> Computer <i class="fas fa-caret-down"></i></a>
        <div class="dropdown-content">
            <a href="add_computer.php">Add Computer</a>
            <a href="manage_computers.php">Manage Computers</a>
        </div>
    </div>
    <div class="dropdown">
        <a href="#"><i class="fas fa-users"></i> User <i class="fas fa-caret-down"></i></a>
        <div class="dropdown-content">
            <a href="add_user.php">Add User</a>
            <a href="manage_user.php">Manage Users</a>
        </div>
    </div>
    <a href="booking.php"><i class="fa-solid fa-window-maximize"></i> Bookings</a>
    <a href="search_user.php"><i class="fas fa-search"></i> Search</a>
    <a href="generate_report.php"><i class="fas fa-chart-line"></i> Reports</a>
</div>

<!-- Main Form -->
<div class="container">
    <div class="form-container">
        <?php
            if (!empty($errors)) {
                echo "<ul style='color:red;'>";
                foreach ($errors as $err) {
                    echo "<li>" . htmlspecialchars($err) . "</li>";
                }
                echo "</ul>";
            } elseif (!empty($message)) {
                echo "<p>$message</p>";
            }
        ?>

        <h2>Add New User</h2>
        <form method="POST" novalidate>
            <label>Name:</label>
            <input type="text" name="name" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required onblur="capitalizeName(this)">
            <div id="name-error" class="field-error"></div>

            <label>Address:</label>
            <textarea name="address" rows="3" required><?= isset($address) ? htmlspecialchars($address) : '' ?></textarea>
            <div id="address-error" class="field-error"></div>

            <label>Mobile Number:</label>
            <input type="text" name="mobile_number" value="<?= isset($mobile_number) ? htmlspecialchars($mobile_number) : '' ?>" required>
            <div id="mobile-error" class="field-error"></div>

            <label>Email:</label>
            <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
            <div id="email-error" class="field-error"></div>

            <button type="submit">Add User</button>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
document.querySelector("form").addEventListener("submit", function (e) {
    let isValid = true;

    const name = document.querySelector('input[name="name"]');
    const address = document.querySelector('textarea[name="address"]');
    const mobile = document.querySelector('input[name="mobile_number"]');
    const email = document.querySelector('input[name="email"]');

    const nameError = document.getElementById("name-error");
    const addressError = document.getElementById("address-error");
    const mobileError = document.getElementById("mobile-error");
    const emailError = document.getElementById("email-error");

    nameError.textContent = "";
    addressError.textContent = "";
    mobileError.textContent = "";
    emailError.textContent = "";

    const nameRegex = /^[A-Za-z ]+$/;
    if (!name.value.trim() || !nameRegex.test(name.value.trim())) {
        nameError.textContent = "Name must contain only letters and spaces.";
        isValid = false;
    }

    if (!address.value.trim() || address.value.trim().length < 10) {
        addressError.textContent = "Address must be at least 10 characters long.";
        isValid = false;
    }

    const mobileRegex = /^[6-9]\d{9}$/;
    if (!mobileRegex.test(mobile.value.trim())) {
        mobileError.textContent = "Mobile must be 10 digits and start with 6, 7, 8, or 9.";
        isValid = false;
    }

    const emailVal = email.value.trim();
    if (!emailVal.endsWith("@gmail.com") || emailVal.indexOf("@") <= 0) {
        emailError.textContent = "Email must be a valid Gmail address (e.g., yourname@gmail.com).";
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});

function capitalizeName(input) {
    input.value = input.value
        .toLowerCase()
        .split(' ')
        .filter(w => w.length > 0)
        .map(w => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');
}
</script>

</body>
</html>

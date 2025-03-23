<?php
// Database connection details
$host = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$database = "crud_example";

$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle actions based on POST data
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // CREATE Operation
    if ($action == "create") {
        $name = $_POST['name'];
        $email = $_POST['email'];

        // Check if user with the same name or email already exists
        $checkQuery = "SELECT * FROM users WHERE name='$name' OR email='$email'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "Error: A user with the same name or email already exists!";
        } else {
            $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
            if (mysqli_query($conn, $sql)) {
                echo "User added successfully!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }

    // READ Operation
    else if ($action == "read") {
        $sql = "SELECT * FROM users";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td><button onclick='editUser(" . $row['id'] . ", \"" . $row['name'] . "\", \"" . $row['email'] . "\")'>Edit</button> ";
                echo "<button onclick='deleteUser(" . $row['id'] . ")'>Delete</button></td>";
                echo "</tr>";
            }
        } else {
            echo "No users found.";
        }
    }

    // UPDATE Operation
    else if ($action == "update") {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo "User updated successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    // DELETE Operation
    else if ($action == "delete") {
        $id = $_POST['id'];

        $sql = "DELETE FROM users WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo "User deleted successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>

<?php
// Database connection details
$host = "localhost";
$username = "root"; // Change this if necessary
$password = "";     
$database = "crud_example";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the raw POST data (the body of the request)
$data = json_decode(file_get_contents('php://input'), true);  // Decode the JSON data

// Check if action is set in the request
if (isset($data['action'])) {
    $action = $data['action'];

    // CREATE Operation
    if ($action == "create") {
        $name = trim($data['name']);
        $email = trim($data['email']);

        // Validate inputs
        if (empty($name) || empty($email)) {
            echo json_encode(["status" => "error", "message" => "Name and Email are required!"]);
            exit();
        }

        // Check for duplicate entries
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? OR email = ?");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "A user with the same name or email already exists!"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User added successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => $stmt->error]);
            }
        }
        $stmt->close();
    }

    // READ Operation
    elseif ($action == "read") {
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $users]);
    }

    // UPDATE Operation
    elseif ($action == "update") {
        $id = $data['id'];
        $name = trim($data['name']);
        $email = trim($data['email']);

        if (empty($id) || empty($name) || empty($email)) {
            echo json_encode(["status" => "error", "message" => "All fields are required!"]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    }

    // DELETE Operation
    elseif ($action == "delete") {
        $id = $data['id'];

        if (empty($id)) {
            echo json_encode(["status" => "error", "message" => "User ID is required!"]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "User deleted successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

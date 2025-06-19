<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Extract ID from URL (e.g., /users/1)
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$id = isset($request[1]) ? intval($request[1]) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT id, name, email, dob FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            echo json_encode($result ?: ["message" => "User not found"]);
        } else {
            $result = $conn->query("SELECT id, name, email, dob FROM users");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        if (!empty($input['name']) && !empty($input['email']) && !empty($input['password']) && !empty($input['dob'])) {
            $name = $input['name'];
            $email = $input['email'];
            $password = password_hash($input['password'], PASSWORD_BCRYPT);
            $dob = $input['dob'];

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, dob) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $dob);
            if ($stmt->execute()) {
                echo json_encode(["message" => "User created successfully"]);
            } else {
                echo json_encode(["error" => $stmt->error]);
            }
        } else {
            echo json_encode(["error" => "Missing fields"]);
        }
        break;

    case 'PUT':
        if ($id && !empty($input)) {
            $fields = [];
            $values = [];

            if (isset($input['name'])) {
                $fields[] = "name=?";
                $values[] = $input['name'];
            }
            if (isset($input['email'])) {
                $fields[] = "email=?";
                $values[] = $input['email'];
            }
            if (isset($input['password'])) {
                $fields[] = "password=?";
                $values[] = password_hash($input['password'], PASSWORD_BCRYPT);
            }
            if (isset($input['dob'])) {
                $fields[] = "dob=?";
                $values[] = $input['dob'];
            }

            if ($fields) {
                $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id=?";
                $values[] = $id;

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat('s', count($values)-1) . 'i', ...$values);

                if ($stmt->execute()) {
                    echo json_encode(["message" => "User updated successfully"]);
                } else {
                    echo json_encode(["error" => $stmt->error]);
                }
            }
        } else {
            echo json_encode(["error" => "Invalid request"]);
        }
        break;

    case 'DELETE':
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "User deleted"]);
            } else {
                echo json_encode(["error" => $stmt->error]);
            }
        } else {
            echo json_encode(["error" => "ID not specified"]);
        }
        break;

    default:
        echo json_encode(["error" => "Unsupported request method"]);
        break;
}

$conn->close();
?>

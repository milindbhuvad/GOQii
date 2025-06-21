<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$valid_user = 'admin';
$valid_pass = 'Admin@123';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My API"');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(["error" => "Authentication required"]);
    exit;
}

if (
    $_SERVER['PHP_AUTH_USER'] !== $valid_user ||
    $_SERVER['PHP_AUTH_PW'] !== $valid_pass
) {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
}

include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
// Extract ID from URL (e.g., /users/1)
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$id = isset($request[2]) ? intval($request[2]) : null;
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

            // Validate name (only letters and spaces, 2-50 characters)
            if (!preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
                echo json_encode(["error" => "Invalid name format"]);
                break;
            }

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(["error" => "Invalid email format"]);
                break;
            }

            // Validate DOB (format: YYYY-MM-DD and logical date)
            $dobRegex = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($dobRegex, $dob) || !strtotime($dob)) {
                echo json_encode(["error" => "Invalid date of birth format"]);
                break;
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, dob) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $dob);
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

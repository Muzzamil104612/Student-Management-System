<?php

include 'db_connnection.php';

// If data is coming as JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['student_name']) || !isset($data['city']) || !isset($data['student_id'])) {
    echo json_encode(array('error' => "Data is not completely sent for student_id, student_name, and city fields"));
    exit;
}

$student_id = (int)$data['student_id'];

// Ensure student_id is a valid numeric value
if (!is_numeric($data['student_id'])) {
    echo json_encode(array('error' => "Invalid student_id. Must be a numeric value."));
    exit;
}

$student_id = (int)$data['student_id'];
$student_name = mysqli_real_escape_string($conn, $data['student_name']);
$city = mysqli_real_escape_string($conn, $data['city']);

// Use prepared statements for security
$query = "INSERT INTO students (student_id, student_name, city) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

$stmt->bind_param("iss", $student_id, $student_name, $city);

if ($stmt->execute()) {
    echo json_encode(array('message' => "Data added successfully for student_id='.$student_id.', student_name='.$student_name.', and city='.$city"));
} else {
    // Log the detailed error on the server
    error_log("Error executing query: " . $query . " Error: " . $conn->error);

    // Provide a generic error message to the client
    echo json_encode(array('error' => "Failed to add data. Please try again later."));
}

$stmt->close();
$conn->close();
?>

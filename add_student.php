<?php

include 'db_connnection.php';

// If data is coming as JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['student_name']) || !isset($data['city']) || !isset($data['student_id'])) {
    echo json_encode(array('error' => "Data is not completely sent for student_id student_name and city fields"));
    exit;
}

$student_id = (int)$data['student_id'];  // Ensure it's cast to integer
$student_name = mysqli_real_escape_string($conn, $data['student_name']);
$city = mysqli_real_escape_string($conn, $data['city']);

// Use single quotes around string values in the SQL query
$query = "INSERT INTO students (student_id, student_name, city) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

$stmt->bind_param("iss", $student_id, $student_name, $city);

if ($stmt->execute()) {
    echo json_encode(array('message' => "Data added successfully for student_id=$student_id, student_name= $student_name and city = $city"));
} else {
    echo json_encode(array('error' => "Error: " . $query . "<br>" . $conn->error));
}

$stmt->close();
$conn->close();
?>

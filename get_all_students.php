
<?php
include 'db_connnection.php';

$data = json_decode(file_get_contents('php://input'));



$query = "SELECT * FROM students ";

// Sorting
$sort_column = isset($data->sort_column) ? mysqli_real_escape_string($conn, $data->sort_column) : 'student_id';
$sort_direction = isset($data->sort_direction) ? mysqli_real_escape_string($conn, $data->sort_direction) : 'ASC';

// Pagination
$page_size = isset($data->page_size) ? max(1, intval($data->page_size)) : 10;
$page = isset($data->page) ? max(1, intval($data->page)) : 1;
$offset = ($page - 1) * $page_size;

// Searching
$search_term = isset($data->search_term) ? mysqli_real_escape_string($conn, $data->search_term) : null;

// Filtering according to city
$filter_city = isset($data->filter_city) ? mysqli_real_escape_string($conn, $data->filter_city) : null;

if (isset($data->sort_direction)) {
    $sort_direction = strtolower($data->sort_direction);
    if ($sort_direction != 'asc' && $sort_direction != 'desc') {
        echo json_encode(array('message' => 'Your provide value for sort_direction is not correct!'));
        exit;
    }
}

if (isset($data->sort_column)) {
    $sort_column = strtolower($data->sort_column);
    if ($sort_column != 'student_id' && $sort_column != 'student_name' && $sort_column != 'city') {
        echo json_encode(array('message' => 'Your provide value for sort_column is not correct!'));
        exit;
    }
}

if ($search_term) {
    $query .= " WHERE student_name LIKE '%$search_term%' ";
}

if ($filter_city) {
    $query .= ($search_term ? " AND " : " WHERE ") . "city = '$filter_city'";
}

if ($sort_column && $sort_direction) {
    $query .= " ORDER BY $sort_column $sort_direction ";
}
if ($page && $page_size) {
    $query .= " LIMIT $offset, $page_size ";
}

$result = $conn->query($query);

if ($result) {
    $students = array();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $result->close();
    echo json_encode(array('students' => $students));
} else {
    echo json_encode(array('error' => 'Error: ' . $query . '<br>' . $conn->error));
}

$conn->close();
?>

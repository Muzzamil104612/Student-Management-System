<?php
include 'db_connnection.php';


$data=json_decode(file_get_contents('php://input'));



echo json_encode(array(
    'message' => 'For Sorting set values for sort_column, sort_direction --
	 For Pagination set page_size, page (which page number) --
	 For searching (by course_name) set search_term --
	 For Filtering (by course_name) set filter_course_name'
));

$query = "SELECT * FROM courses ";

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
$filter_course_name = isset($data->filter_course_name) ? mysqli_real_escape_string($conn, $data->filter_course_name) : null;

if (isset($data->sort_direction)) {
    $sort_direction = strtolower($data->sort_direction);
    if ($sort_direction != 'asc' && $sort_direction != 'desc') {
        echo json_encode(array('message' => 'Your provide value for sort_direction is not correct!'));
        exit;
    }
}

if (isset($data->sort_column)) {
    $sort_column = strtolower($data->sort_column);
    if ($sort_column!='course_id' && $sort_column !='course_name' && $sort_column !='credit_hours') {
        echo json_encode(array('message' => 'Your provide value for sort_column is not correct!'));
        exit;
    }
}

if ($search_term) {
    $query .= " WHERE course_name LIKE '%$search_term%' ";
}

if ($filter_course_name) {
    $query .= ($search_term ? " AND " : " WHERE ") . "course_name = '$filter_course_name'";
}

if ($sort_column && $sort_direction) {
    $query .= " ORDER BY $sort_column $sort_direction ";
}
if ($page && $page_size) {
    $query .= " LIMIT $offset, $page_size ";
}








$result=$conn->query($query);

if($result){
	$courses=array();

	while($row =$result->fetch_assoc()){
		$courses[]=$row;
	}

	$result->close();
	echo json_encode(array('courses'=>$courses));

}
else{
	echo json_encode(array('error'=>'Error :'.$query."<br>".$conn->error()));
}



$conn->close();

?>
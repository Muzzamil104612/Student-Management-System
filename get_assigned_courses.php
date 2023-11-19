<?php


include 'db_connnection.php';


$data=json_decode(file_get_contents('php://input'));


$query = "SELECT  sc.enrollment_id, s.student_name, c.course_name 
          FROM student_courses as sc
          INNER JOIN courses as c ON sc.course_id = c.course_id
          INNER JOIN students as s ON sc.student_id = s.student_id
          WHERE sc.student_id = ?
         ";



$sort_column=(isset($data->sort_column))?mysqli_real_escape_string($conn,$data->sort_column):'sc.enrollement_id';
$sort_direction=(isset($data->sort_direction))?mysqli_real_escape_string($conn,$data->sort_direction):'ASC';




// Pagination
$page_size = isset($data->page_size) ? max(1, intval($data->page_size)) : 10;
$page = isset($data->page) ? max(1, intval($data->page)) : 1;
$offset = ($page - 1) * $page_size;

// Searching
$search_term = isset($data->search_term) ? mysqli_real_escape_string($conn, $data->search_term) : null;

// Filtering according to city
$filter_course_name = isset($data->filter_course_name) ? mysqli_real_escape_string($conn, $data->filter_course_name) : null;



if(isset($data->sort_direction)){

	$sort_direction=strtolower($data->sort_direction);
	if($sort_direction!='asc' && $sort_direction !='desc'){
	echo json_encode(array('message'=>'Your provide value for sort_direction  is not correct  !'));
	exit;
}
}

if(isset($data->sort_column)){
 $sort_column=strtolower($data->sort_column);
if($sort_column!='student_name' && $sort_column !='course_name' && $sort_column !='enrollement_id'&& $sort_column !='student_id' && $sort_column !='course_id'){
	echo json_encode(array('message'=>'Your provide value for sort_column is not correct !'));
	exit;
}
elseif ($sort_column=='student_name') {
	$sort_column='s.student_name';
}
elseif ($sort_column=='course_name') {
	$sort_column='c.course_name';
}
elseif ($sort_column=='enrollement_id') {
	$sort_column='sc.enrollment_id';
}
elseif ($sort_column=='student_id') {
	$sort_column='sc.student_id';
}
elseif ($sort_column=='course_id') {
	$sort_column='sc.course_id';
}

}


if ($search_term) {
    $query .= " AND s.student_name LIKE '%$search_term%' ";
}

if ($filter_course_name) {
    $query .= " AND c.course_name = '$filter_course_name'";
}

if ($sort_column && $sort_direction) {
    $query .= " ORDER BY $sort_column $sort_direction ";
}
if ($page && $page_size) {
    $query .= " LIMIT $offset, $page_size ";
}












if(!isset($data->student_id) ){
	echo json_encode(array('error'=>'Please Give the student_id '));
	exit;
}

$student_id=(int)$data->student_id;

 

$stmt=$conn->prepare($query);
$stmt->bind_param("i",$student_id);
$stmt->execute();

$result=$stmt->get_result();




if($result){

	$assigned_courses=array();

	while($row = $result->fetch_assoc()){
		$assigned_courses[]=$row;
	}


	$result->close();

	echo json_encode(array('assigned_courses'=>$assigned_courses));
}

else{

	echo json_encode(array('error'=>'Error: '.$query."<br>".$conn->error()));
}


$conn->close();


?>
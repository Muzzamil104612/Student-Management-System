<?php

require_once 'db_connnection.php';

$data=json_decode(file_get_contents("php://input"),true);

if( !isset($data['course_id']) || !isset($data['course_name']) || !isset($data['credit_hours'])){
	echo json_encode(array('error'=>'Please Send complete data for course_id, course_name and credit_hours!'));
	exit;
}

$course_id=(int)$data['course_id'];
$course_name=mysqli_real_escape_string($conn,$data['course_name']);
$credit_hours=(int)$data['credit_hours'];

$query="INSERT INTO courses values (?,?,?)";
$stmt=$conn->prepare($query);
$stmt->bind_param("isi",$course_id,$course_name,$credit_hours);

if($stmt->execute()){
	echo json_encode(array('message'=>'Successfully data is inserted for course_id= '.$course_id.', course_name= '.$course_name.' ,credit_hours='.$credit_hours));

}
else{
	 echo json_encode(array('error'=>'Error:'.$query."<br>". $conn->error()));
}

$stmt->close();
$conn->close();



?>
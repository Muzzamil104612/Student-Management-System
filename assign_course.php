<?php
include 'db_connnection.php';

$data=json_decode(file_get_contents("php://input"),true);

if( !isset($data['student_name']) || !isset($data['course_name']) || !isset($data['enrollement_id'])){
	echo json_encode(array('error'=>'Please Give values for enrollement_id,student_name and course_name'));
	exit;
}

$enrollement_id=(int)$data['enrollement_id'];
$student_name=mysqli_real_escape_string($conn,$data['student_name']);
$course_name=mysqli_real_escape_string($conn,$data['course_name']);



$get_student_id="SELECT student_id FROM students WHERE student_name=?";
$student_stmt=$conn->prepare($get_student_id);
$student_stmt->bind_param("s",$student_name);
$student_stmt->execute();
$student_result=$student_stmt->get_result();
if($student_result->num_rows==0){
	echo json_encode(array('error'=>'This Student does not exits in database, Please provide another value for student_name !'));
	exit;
}

$student_row=$student_result->fetch_assoc();
$student_id=(int)$student_row['student_id'];
$student_stmt->close();

$get_course_id="SELECT course_id FROM courses WHERE course_name=?";
$course_stmt=$conn->prepare($get_course_id);
$course_stmt->bind_param("s",$course_name);
$course_stmt->execute();
$course_result=$course_stmt->get_result();
if($course_result->num_rows==0){
	echo json_encode(array('error'=>'This Course does not exits in database, Please provide another value for course_name !'));
	exit;
}
$course_row=$course_result->fetch_assoc();
$course_id=(int)$course_row['course_id'];
$course_stmt->close();

$query="INSERT into student_courses values(?,?,?)";
$stmt=$conn->prepare($query);
$stmt->bind_param("iii",$enrollement_id,$student_id,$course_id);

if($stmt->execute()){
	echo json_encode(array('message'=>'Successfully  '.$course_name .' has assigned to '. $student_name .' against enrollement_id= '.$enrollement_id));
}
else{
	echo json_encode(array('error'=>'Error: '.$query."<br>".$conn->error));
}


$stmt->close();
$conn->close();






?>
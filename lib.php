<?php
/**
 * Lib functions
 *
 * @package    local
 * @subpackage students
 */
defined('MOODLE_INTERNAL') || die();
/**
 * If $all_students = true 	Get all people who attended courses,  
 * If $all_students = false Get all people who attended courses and publishing_status=1,   
 * If $get_status 	= true 	Get students publication status.
 * 
 * @param bool $all_students 	True or false
 * @param bool $get_status 		True or false
 * 
 * @return array
 */
function get_students_data($all_students = true, $get_status = true){
	global $DB;
	
	$get_records = 'SELECT us.id, us.firstname, us.lastname, us.email, c.shortname '.($get_status == true ? ', st.publishing_status AS status' : '').'
	  			 	FROM {user} AS us
				 	JOIN {course} AS c
				 	JOIN {students} AS st
				 	JOIN {course_categories} AS cc
				 	ON c.category = cc.id
				 	JOIN {context} AS ctx 
				 	ON (c.id = ctx.instanceid AND ctx.contextlevel = 50)
				 	JOIN {role_assignments} AS ra
				 	'.($get_status == true ? 'WHERE (ra.contextid = ctx.id AND ra.userid = us.id AND st.userid = us.id)' : 'ON (ra.contextid = ctx.id AND ra.userid = us.id)').'
				 	'.($all_students == true ? '' : 'WHERE (st.publishing_status = 1 AND st.userid = us.id)').'';
	$records = $DB->get_records_sql($get_records);
	foreach ($records as $record){
		if($get_status == true){
			$data[] = array ("id"=>$record->id, "name"=>$record->firstname.' '.$record->lastname, "email"=>$record->email, "course"=>$record->shortname, "status"=>$record->status);
		} else {
			$data[] = array ("id"=>$record->id, "name"=>$record->firstname.' '.$record->lastname, "email"=>$record->email, "course"=>$record->shortname);
		}
	}
	if(isset($data)){
		return $data;
	}
}
/**
 * Save students publication status
 * 
 * @return void
 */
function save_publication_status(){
	global $DB;
	
	$publishing_status = optional_param('publish', null, PARAM_INT);
	$dataobject = new stdClass();
	foreach($publishing_status as $user_id=>$status){
		$student_id = "SELECT id FROM {students} WHERE userid = '.$user_id.'";
		$student = $DB->get_record_sql($student_id);
		$dataobject->id = $student->id;
		$dataobject->userid = $user_id;
		$dataobject->publishing_status = $status;
		$DB->update_record('students', $dataobject);
	}
}
/**
 * Adding new link to the navigation block
 * 
 * @param global_navigation $navigation
 * @return navigation_node 
 */
function students_extends_navigation(global_navigation $navigation) {
	global $CFG;
	
	$url = new moodle_url($CFG->wwwroot.'/local/students/index.php');
	$students_link = $navigation->add(get_string('students_nav_link','local_students'), $url);
}

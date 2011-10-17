<?php
require_once('../../config.php');
require_once('lib.php');

require_login();

if(isset($_POST['publish'])){
	echo save_publication_status();
}

$url = new moodle_url($CFG->wwwroot.'/local/students/edit_status.php');
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_url($url);
$PAGE->set_title(get_string('set_status_page_title','local_students'));
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

echo '	<form action="edit_status.php" method="post" id="adminsettings">
			<div class="box generalbox adminwarning">
				<h2 class="main">'.get_string('set_status_page_title','local_students').'</h2>';
					$table = new html_table();
					$table->attributes['class'] = 'generaltable';
					$table->head  = array (get_string('id','local_students'), get_string('students','local_students'), get_string('e-mail','local_students'), get_string('company','local_students'), get_string('course','local_students'), get_string('publishing_status','local_students'));
					$table->align = array ('center', 'left', 'left', 'left', 'left', 'center');
					$table->width = '100%';
					$students = get_students_data(true, true);
					foreach ($students as $i=>$student){
						if($student['status'] == 1){
							$checkbox = '<input type="checkbox" name="publish['.$student['id'].']" value="1" checked="checked" />';
						} else {
							$checkbox = '<input type="checkbox" name="publish['.$student['id'].']" value="1" />';
						}
						$table->data[] = array ($i+1, $student['name'], $student['email'], ' ', $student['course'], '<input type="hidden" name="publish['.$student['id'].']" value="0" />'.$checkbox.'');
					}
					echo html_writer::table($table);
echo '	  <hr /><div class="form-buttons">
					<input class="form-submit" type="submit" value="'.get_string('save_status','local_students').'" />
				</div>
			</div>
		</form> '; 

echo $OUTPUT->footer();


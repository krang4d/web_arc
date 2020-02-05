<?
//$ob_file = fopen('/tmp/php.txt','w');
//ob_start('ob_file_callback');
function ob_file_callback($buffer)
{
  global $ob_file;
  fwrite($ob_file,$buffer);
}

/*	$r = array(
                'status' => "error",
                'message' => "Run starts correctly for ",
                'data' => array()
                        );
		echo json_encode($r);
		exit(0);
*/

require_once("tcp.php");


if($_POST && $_POST['request']) {


$rr = json_decode($_POST['request']);

$file_id = $rr->record->file_id;

//var_dump($rr);
//ob_end_flush();
if(isset($file_id) && intval($file_id) >= 0) {
	$r = exec_command("run ".$file_id);
//	var_dump($r);
//	ob_end_flush();
/*
	$r = array(
                'status' => "error",
                'message' => "Run starts correctly for ".$file_id,
                'data' => array()
                        );
		echo json_encode($r);
		exit(0);
*/
		}

//var_dump($r);
} else
	$r = array(
		'status' => "error",
		'message' => "Method POST is empty",
		'data' => array()
		);
echo json_encode($r);
exit(0);
?>
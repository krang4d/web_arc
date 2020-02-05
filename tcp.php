<?
define('SRV_ADDR', '172.16.1.28');
define('SRV_PORT', 1234);

function pars($st) {
$ret = array();
$ret['status'] = "success";
$ret['message'] = "Hello from tcp.php. I am OK!";
$ret['state'] = 200;	// HTTP-like error code
$ret['data'] = array();
$sd = explode("\n", $st);
foreach($sd as $k => $s) {
	$s_ = explode(" ", $s);
	if($s_[0] == 'ffd')  {
	//	echo("FFD found\n");
	//	echo("Return code: ".intval($s_[1]));
	//	echo("\n");
		$s__ = array_slice($s_, 2);
		$z = implode(" ", $s__);
		if(strncmp($z, "OK", 2) == 0) break;
		$ret['state'] = intval($s_[1]);
		$ret['status'] = "success";
		$ret['message'] = "OK";
		$ret['data'][] =  $z;
		} else {
		$ret['status'] = "error";
		$ret['state'] = 570;
		$ret['message'] = "Bad daemon response";
		}
	}
return $ret;
}


function tcp_connect($addr, $port) {
//$socket = fsockopen($addr, $port, $errno, $errstr, 10);
$socket = @stream_socket_client("tcp://".$addr.":".$port, $errno, $errstr, 30);
if(!$socket)
	return null;
@stream_set_blocking($socket, FALSE);
//socket_set_blocking($socket, 0);
//stream_set_blocking(STDIN, 0);
return $socket;
}



function socket_command($socket, $command) {
$ret = array();
$ret['status'] = "error";
$ret['message'] = "Socket error";
$ret['data'] = array();

	if($command == "") {
		$ret['status'] = "error";
                $ret['message'] =  "Missing command";
		return $ret;
		}
$read   = array( $socket );
$write  = NULL;
$except = NULL;

	if(!is_resource($socket)) {
		$ret['status'] = "error";
		$ret['message'] = "Socket connection error";
		return $ret;
		}

// first, prompt
$num_changed_streams = @stream_select($read, $write, $except, 2);
	if(feof($socket)) {
		$ret['status'] = "error";
		$ret['message'] =  "Socket closed";
		return $ret;
		}
	if(false === $num_changed_streams) {
		$ret['status'] = "error";
		$ret['message'] =  "Socket wait error";
		return $ret;
		}

	if ($num_changed_streams > 0) {
		$data = fread($socket, 4096);
//		echo("Got connected:".$data."\n");
		if($data !== "") {
			$ret['status'] = "success";
//			$ret['data'][] = $data;
			if(strncmp($data, "ffd 200", 4) != 0) {
//				echo("ERRRRRRRRR: '".$data."'\n");
				$ret['status'] = "error";
				$ret['message'] =  "Unknown proto";
				return $ret;			
				}
			
			}
		} else {
		$ret['status'] = "error";
		$ret['message'] =  "Socket wait error";
		return $ret;
		}

$read   = NULL;
$write  = array( $socket );
$except = NULL;

$num_changed_streams = stream_select($read, $write, $except, 2);
//echo("str: ".$num_changed_streams."\n");
//var_dump($write);
//	echo("Sending command '".trim($command)."'\n");
	$comm = trim($command)."\n";
	$t =fwrite($socket, $comm, strlen($comm));

//sleep(1);
$read   = array( $socket );
$write  = NULL;
$except = NULL;

	if(!is_resource($socket)) {
		$ret['status'] = "error";
		$ret['message'] = "Socket connection error";
		return $ret;
		}

// now, result
$num_changed_streams = @stream_select($read, $write, $except, 2);
//echo("str: ".$num_changed_streams."\n");
	if(feof($socket)) {
		$ret['status'] = "error";
		$ret['message'] =  "Socket closed";
		return $ret;
		}
	if(false === $num_changed_streams) {
		$ret['status'] = "error";
		$ret['message'] =  "Socket wait error";
		return $ret;
		}

	if ($num_changed_streams > 0) {
//		for($i = 0; $i < 10; $i++) {

		while(true) {
		$num_changed_streams = @stream_select($read, $write, $except, 0, 50000);
		if($num_changed_streams === false) break;
		$da = fread($socket, 4096);
//		echo("Got data:".$da."\n");
//		if($da == "") break;
		if($da !== "") {
			$ret['status'] = "success";
			$ret['data'][] = $da;
		}
		}
		
		} else {
		$ret['status'] = "error";
		$ret['message'] =  "Socket wait error";
		return $ret;
		}


return $ret;
}

function exec_command($command){
$ret = array(
	'status' => "error",
	'message' => "Socket error",
	'state' => 400,
	'data' => array()
	);

$sock = tcp_connect(SRV_ADDR, SRV_PORT);
if($sock) {
	$r = socket_command($sock, $command);
//	var_dump($r);
	if($r['status'] != "error") {
	$str = implode("", $r['data']);
//	echo($str);
	$ret = pars($str);
//	var_dump($ret);
	}
	}
return $ret;
}

//$r = exec_command("help");
//var_dump($r);

<!DOCTYPE html>
<html>
<head>
    <title>Оповещения</title>
    <link rel="stylesheet" type="text/css" href="w2ui-1.5.rc1.min.css" />
    <script src="js/jquery-3.2.1.js"></script>
    <script src="jquery.form.js"></script>
    <script type="text/javascript" src="w2ui-1.5.rc1.js"></script>
    <script src="js/Chart.min.js"></script>
</head>
<body>

<?

// get only [Signal N] from init array
function get_streams($k){
preg_match('/^Stream[ ]*(?P<digit>\d+)/', $k, $matches);
if(!empty($matches))
	return true;
return false;
}

// get only [Audio N] from init array
function get_streams_a($k){
preg_match('/^Audio[ ]*(?P<digit>\d+)/', $k, $matches);
if(!empty($matches))
	return true;
return false;
}

// get only [Video N] from init array
function get_streams_v($k){
preg_match('/^Video[ ]*(?P<digit>\d+)/', $k, $matches);
if(!empty($matches))
	return true;
return false;
}


$ini_array = parse_ini_file("files_new.ini", true);

$signals = array();

if($ini_array && count($ini_array) > 0) {
	$signalz = array_filter($ini_array, 'get_streams', ARRAY_FILTER_USE_KEY);
	if($signalz && count($signalz) > 0) {
         $i=0;
         foreach($signalz as $k => $v) {
            $signals[$i] = array(
            'id' => $v['id'],
            'name' => str_replace("\r", "", str_replace("\n","", nl2br($v['name']))),
            'comments' => str_replace("\r", "", str_replace("\n","", nl2br($v['comments'])))
            );
            $i++;
         }
	}
} else { // default values
   for($i=0; $i < 13; $i++) {
		$signals[$i] = array(
			'id' => $i,
			'name' => 'Sig'.$i,
			'comments' => 'comment'.$i,
			);
   }
//var_dump($signals);
}

$items = "";
	foreach($signals as $s => $v) {
// $inner = strtr($inner, "\n"," "); // do not forget! OLD js does not like '\n', new likes if multiline string is in ``
//		$items .= "{ id: 'html".$v['id']."', did: ".$v['id'].", text: '".$v['name']."', img: 'icon-page' , inner_text: '".$inner."'},\n";
 $inner = "Nothing...";
		$items .= "{ id: 'html".$v['id']."', did: ".$v['id'].", text: '".$v['name']."', img: 'icon-page' , inner_text: '".$inner."'},\n";
		}
   print_r($signals);
?>


<script type="text/javascript">
var signalz =[];
var num_sig=0;

function onAjaSuccess(dat)
{
var zz = JSON.parse(dat);
  // Здесь мы получаем данные, отправленные сервером и выводим их на экран.
//  alert(dat);
//  console.log(zz.data);
  zz['data'].forEach(function(el) {
//  console.log(a + '----' + el);
//  if(el == 1) console.log(a + '----' + el);
  if(el[2] == "s") { // signals
  bt = document.getElementById('run' + el[0]);
  fm  = document.getElementById('form' + el[0]);
  if(bt && fm) {
//    console.log(bt);
//    console.log(el);
    idd = 'form' + el[0];
    if(el[1] == 1) {
        if(!$('#run' + el[0]).hasClass('w2ui-btn-red')) $('#run' + el[0]).toggleClass('w2ui-btn-red');
	$('#run' + al[0]).html('Стоп');
          w2ui[idd].url = '/stop.php';
          
        } else {
        if(!$('#run' + el[0]).hasClass('w2ui-btn-green')) 	$('#run' + el[0]).toggleClass('w2ui-btn-green');
	$('#run' + el[0]).html('Пуск');
          w2ui[idd].url = '/run_test.php';
          }

    }
    }
  });

}

setInterval(function ()
{
//w2ui['grid'].load('/@nc/GetData');
//var daa =
var jqxh = $.post("status1.php", {param1: "param1", param2: 2}, onAjaSuccess);
//  .success(function() { alert("Успешное выполнение"); })
//  .error(function() { alert("Ошибка выполнения"); })
//  .complete(function() { alert("Завершение выполнения"); });
//console.log(daa);

//$.post("/status.php",
//  {
//    param1: "param1",
//    param2: 2
//  },
//  onAjaSuccess
//)
//.error(function() { alert("Ошибка выполнения"); })
}, 5000);

<?
require_once("tcp.php");

function explode_values(&$value)
{
    $value = trim($value);
    $v = explode(' ', $value);
    $value = intval($v[1]);
}

$statuz = exec_command("status");

array_walk($statuz['data'], 'explode_values');

foreach($signals as $k => $v) {
          if($statuz['data'][$v['id']]) $soo = "run"; else $soo = "stop";
          echo("signalz[".$v['id']."] = { id: '".$v['id']."', name: '".$v['name']."', comments: '".$v['comments']."', shown: false, status: '".$soo."'};\n");
          }

echo("var num_sig=".count($signals).";\n");



?>
//];
</script>

<div id="main" style="width: 100%; height: 400px;"></div>
<canvas id=chartplot width="400" height="200"></canvas>

<script type="text/javascript">
var ctx = document.getElementById('chartplot').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['1', '2', '3', '4', '5', '6'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            fill: false,
            borderColor: [
                'rgba(255, 99, 132, 1)',
            ]
        }]
    },
});
</script>

<script type="text/javascript">
// widget configuration
var config = {
    layout: {
        name: 'layout',
        padding: 0,
        panels: [
            { type: 'left', resizable: true, minSize: 120 },
            { type: 'main', minSize: 400, overflow: 'hidden' },
        ]
    },
    sidebar: {
        name: 'sidebar',
        nodes: [ 
            { id: 'general', text: 'Cигналы', group: true, expanded: true, nodes: [
            <?
            echo($items);
            
            ?>
            
            ]}
        ],
        onClick: function (event) {
		// check if the object exists
		    var nd = true;
		    idd = 'form' + event.node.did;
		    btnAdd = document.getElementById(idd);
//		    console.log("pre:" + btnAdd);
                    if(btnAdd) nd = false;
//                    btnAdd = document.getElementById(idd);
//		    console.log("post:" + btnAdd);
                    if(nd)
                    	defineForm(event.node.did);	// render; if exists, do nothing
		    w2ui[idd].render(w2ui.layout.el('main')); // render to the 'main' box
                    $(w2ui.layout.el('main'))
                        .css({ 
                            'border-left': '1px solid silver'
                        });
        }
    },
};

function defineForm(id) {
    idd = 'form' + id;
    if(!signalz[id].shown) {	// not yet rendered
	console.log("Create");
    $().w2form({
        name  : 'form' + id,
        url   : '/run_test.php',
        fields: [
            { field: 'file_id',   type: 'hidden', value: id},
            { field: 'zoooka',   type: 'text', value: 'HRENOTA'},
//            { field: 'upload',   type: 'w2ui-btn'}
        ],
        formHTML: '<div>'+
		  '<div id="form' + id + '" style="width: 550px;">' +
		  '	<div class="w2ui-page page-0" style="background: #eee;">'+
		  '	<br/><br/><br/>' +
		  '	' + signalz[id].comments +
		  '	<br/><br/><br/>' +
		  '	<div class="w2ui-field">' +
		  '	<input name="file_id" type="hidden"/>' +
		  '	</div>' +
		  '      <div class="w2ui-field  w2ui-span2" id="zaza' + id +'"> ' +
	          '      <button class="w2ui-btn w2ui-btn-green" id="run'+ id +'" name="run">Пуск</button>'+
		  '      </div>' +
		  '      <div style="height: 50px;"></div>'+
		  '      <div class="w2ui-field  w2ui-span2" id="zoooka' + id +'"> ' +
	          '      <input type="text" id="zoooka" name="zoooka">'+
		  '      </div>' +
        '      <div style="height: 50px;"></div>'+
		  '  </div>' +
	          '  <!-- <div class="w2ui-field  w2ui-span2" >'+
	          ' <div class="w2ui-buttons">' +
	          ' <button class="w2ui-btn"  onclick="openPopup('+ id +')" name="upload">Upload</button>' +
	          ' </div>' +
		  '</div> -->' +
		  '</div>' +
		  '</div>'
		  ,
        record: {
        	file_id: id
        	},
	onRender: function(event) {
		event.done(function () {
//            	console.log('object '+ this.name + ' is rendered, status:' + signalz[id].status);
		if(signalz[id].status == 'run' ) {
			$('#run' + id).toggleClass('w2ui-btn-red');
			$('#run' + id).html('Стоп');
			this.url = '/stop.php';
			} else
		if(signalz[id].status == 'stop' ) {
//			$('#run' + id).toggleClass('w2ui-btn-green');
			$('#run' + id).html('Пуск');
			this.url = 'run_test.php';
			}
		bt = document.getElementById('run' + id);
		console.log('object '+ this.name + ' is rendered, status:' + signalz[id].status);
		console.log(bt);
		});
            	},
        actions: {
            reset: function () {
            	console.log("RESET");
                this.clear();
            },
            run: function () {
            	console.log("RUN");
//            	bt = document.getElementById('run');
//		bt = w2ui[idd];
//            	console.log(bt.get('run'));

//            	bt.get('run').style = "w2ui-btn w2ui-btn-red";
//            	w2ui['run'].render();
		this.save(function(data) {
//			if (typeof data.errors != 'undefined') {
//				Main.showServerResponseErrors(form, data.errors);
				console.log("Got response for" + id + ":");
				console.log(data);
//				console.log(data.errors);
//		bt = document.getElementById('zaza' + id);
//		bt.innerHTML = '<button class="w2ui-btn w2ui-btn-red" id="run" name="run">Стоп</button>';
//		bt = document.getElementById('zaza');
		idd = 'form' + id;
		fr = w2ui[idd];
//		console.log(fr);
// 		fr.url = '/stop.php';
//		$('#run').toggleClass('w2ui-btn w2ui-btn-red');
		$('#run' + id).toggleClass('w2ui-btn-red');
//		bt = document.getElementById('run');
		if(signalz[id].status == 'run') {
			signalz[id].status = 'stop';
			$('#run' + id).html('Пуск');
			fr.url = 'run_test.php';
			}
			else {
			$('#run' + id).html('Стоп');
			fr.url = 'stop.php';
			signalz[id].status = 'run';
			}
//		console.log(bt);
		
//				}
		});
//                this.save();
            },
            save: function () {
            	console.log("SAVE");
                this.save();
            }
        }
    });
    signalz[id].shown = true;
    }
};

$(function () {
    // initialization
    $('#main').w2layout(config.layout);
    w2ui.layout.content('left', $().w2sidebar(config.sidebar));
});

</script>


<style>
.progress { position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
.bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
.percent { position:absolute; display:inline-block; top:3px; left:48%; }
</style>
<script type="text/javascript">
function openPopup(idd) {
    w2popup.open({
        title     : 'Popup Title',
        body      : '<div class="w2ui-centered"><form id="myForm" name="myForm" enctype="multipart/form-data" method="POST" action="upload.php">'+
        	'<lable>Signal name: </label><input type="text" name="name_var" width="40" value="' + signalz[idd].name +'"><br/>'+
        	'<label>Comments: </label><textarea name="comments_var" rows="5" cols="30">' + signalz[idd].comments +'</textarea><br/><br/>'+
		'<input id="myfilefield" type="file" name="file">'+
		'<input type="hidden" id="id_var" name="id_var" value="'+ idd +'">'+
		'<br/><br/><input type="submit"><br/></form><br/><br/>'+
		 '<div class="progress"><div class="bar"></div><div class="percent">0%</div></div>'+
		 '<div id="status"></div>'+
		'</div>',
        buttons   : '<button class="w2ui-btn" onclick="w2popup.close();">Close</button> ',
        width     : 600,
        height    : 400,
        overflow  : 'hidden',
        color     : '#333',
        speed     : '0.3',
        opacity   : '0.8',
        modal     : true,
        showClose : true,
        showMax   : true,
        onOpen    : function (event) { console.log('open'); },
        onClose   : function (event) { console.log('close'); },
        onMax     : function (event) { console.log('max'); },
        onMin     : function (event) { console.log('min'); },
        onKeydown : function (event) { console.log('keydown'); }
    });
//    document.getElementById('myfilefield').onchange = function() {
//  this.form.submit();
//	 $('#myForm').ajaxForm(function() { 
//                alert("Thank you for your comment!"); 
//                
//            });
        (function() {
    
        var bar = $('.bar');
        var percent = $('.percent');
        var status = $('#status');
   
$('#myForm').ajaxForm({
    beforeSend: function() {
        status.empty();
        var percentVal = '0%';
        bar.width(percentVal)
        percent.html(percentVal);
        w2popup.lock('Uploading', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
        var percentVal = percentComplete + '%';
        bar.width(percentVal)
        percent.html(percentVal);
    },
    success: function() {
        var percentVal = '100%';
        bar.width(percentVal)
        percent.html(percentVal);
        w2popup.unlock();
    },
	complete: function(xhr) {
		status.html(xhr.responseText);
	}
}); 

})(); 
	//};
}

</script>
</body>
</html>

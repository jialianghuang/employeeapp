<!DOCTYPE HTML>
<html>
<head>
  <title>Schedule</title>

  <style type="text/css">
    body, html {
      font-family: sans-serif;
    
    }
    .vis-text {
      font-weight:bold;
    }
    .vis-inner {
      font-weight:bold;
    }
    .vis-item.expected {
            background-color: transparent;
            border-style: dashed!important;
            z-index: 0;
        }
        .vis-item.vis-selected {
            opacity: 0.6;
        }
        .vis-item.vis-background.marker {
            border-left: 2px solid green;
        }
  </style>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css" />
  
</head>
<body>

<?php
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
$actemps = DB::select('select * from employee');

$actempc=0;
foreach($actemps as $actemp){
$employee[$actempc]['name'] = $actemp->EmpName;
$employee[$actempc]['dept'] = $actemp->EmpDept;
$actempc++;
}
$timeoffs = DB::select('select * from timeoff');
$tfc=0;
foreach($timeoffs as $timeoff){
$timeoffa[$tfc]['name'] = $timeoff->EmpID;
$timeoffa[$tfc]['start'] = $timeoff->Start;
$timeoffa[$tfc]['end'] = $timeoff->End;
$tfc++;
}

date_default_timezone_set('America/Los_Angeles');
$currenttime = date('Y-m-d\TH:i:s').'.000Z';

$punchcolor = '#B6CDE5';
$schedules = DB::select('select * from schedule ');
$EmpNamelist = DB::select('select DISTINCT EmpName from schedule ');
$nc = 0;
foreach($EmpNamelist as $AllEmpName){
  $EmpName[$nc]['name'] = $AllEmpName->EmpName;
  $nc++;
}
$punches = DB::select('select * from punch ');
$oip = 0;
$isstart = 1;
foreach($punches as $punch){
$op[$oip]['name'] = $punch->EmpName;
$op[$oip]['timestamp'] = $punch->TimeStamp;
$oip++;
  }
  $lastStampDate = NULL;
  $si = 0;
  for($i=0;$i<$oip-1;$i++){
      if($op[$i]['name']==$op[$i+1]['name']){
            $TimeStampDate = explode(" ",trim($op[$i]['timestamp']));
            $nextStampDate = explode(" ",trim($op[$i+1]['timestamp']));
            if($TimeStampDate[0]==$nextStampDate[0]){
                if($isstart == 1){
                    $opp[$si]['name']=$op[$i]['name'];
                    $opp[$si]['timestampsta'] = $TimeStampDate[0].'T'.$TimeStampDate[1].'Z';
                    $tempend = explode(" ",trim($op[$i+1]['timestamp']));
                    $opp[$si]['timestampend'] = $tempend[0].'T'.$tempend[1].'Z';
                    $i++;
                    $si++;
                }
            }
      }else{
        $TimeStampDate = explode(" ",trim($op[$i]['timestamp']));
        $opp[$si]['name']=$op[$i]['name'];
        $opp[$si]['timestampsta'] = $TimeStampDate[0].'T'.$TimeStampDate[1].'Z';
        $opp[$si]['timestampend'] = $currenttime;
        $si++;
      }
  }
  
if(isset($op[$i]['name'])){
  $TimeStampDate = explode(" ",trim($op[$i]['timestamp']));
  $opp[$si]['name'] = $op[$i]['name'];
  $opp[$si]['timestampsta'] = $TimeStampDate[0].'T'.$TimeStampDate[1].'Z';
        $opp[$si]['timestampend'] = $currenttime;
        $si++;
}


$oi=0;
foreach($schedules as $s){

$oc[$oi]['NAME'] = $s->EmpName;
$oc[$oi]['date'] = $s->EmpDate;
$oc[$oi]['start'] = $s->EmpStart;
$oc[$oi]['end'] = $s->EmpEnd;
$oi++;
}
for($ki=0;$ki<$oi;$ki++){
  for($q=0;$q<$actempc;$q++){
    if(trim($employee[$q]['name'])==trim($oc[$ki]['NAME'])){

      if(trim($employee[$q]['dept'])=='Cashier')
      $oc[$ki]['color'] = '#C39BD3';
      else if(trim($employee[$q]['dept'])=='Warehouse')
      $oc[$ki]['color'] = '#797D7F';
      else if(trim($employee[$q]['dept'])=='Office')
      $oc[$ki]['color'] = '#82E0AA';
      else if(trim($employee[$q]['dept'])=='General Labor')
      $oc[$ki]['color'] = '#5DADE2';
      else if(trim($employee[$q]['dept'])=='Packing')
      $oc[$ki]['color'] = '#E67E22';
      
      break;
}else{
  $oc[$ki]['color'] = '#22e6e2';
}
  }
}

for($ki=0;$ki<$si;$ki++){
  for($q=0;$q<$actempc;$q++){
    if(trim($employee[$q]['name'])==trim($opp[$ki]['name'])){

      if(trim($employee[$q]['dept'])=='Cashier')
      $opp[$ki]['color'] = '#C39BD3';
      else if(trim($employee[$q]['dept'])=='Warehouse')
      $opp[$ki]['color'] = '#797D7F';
      else if(trim($employee[$q]['dept'])=='Office')
      $opp[$ki]['color'] = '#82E0AA';
      else if(trim($employee[$q]['dept'])=='General Labor')
      $opp[$ki]['color'] = '#5DADE2';
      else if(trim($employee[$q]['dept'])=='Packing')
      $opp[$ki]['color'] = '#E67E22';
      
      break;
}else{
  $opp[$ki]['color'] = '#22e6e2';
}
  }
}

?>

<p>
<b>Schedule</b>
</p>
<div>
<span style="width:20px;height:20px;background:#C39BD3;border:1px solid #C39BD3;margin-right:20px;display:inline-block"></span>
<span style="margin-right:20px;margin-bottom:10px;">Cashier</span>
<span style="width:20px;height:20px;background:#797D7F;border:1px solid #797D7F;margin-right:20px;display:inline-block"></span>
<span style="margin-right:20px;margin-bottom:10px;">Warehouse</span>
<span style="width:20px;height:20px;background:#82E0AA;border:1px solid #82E0AA;margin-right:20px;display:inline-block"></span>
<span style="margin-right:20px;margin-bottom:10px;">Office</span>
<span style="width:20px;height:20px;background:#5DADE2;border:1px solid #5DADE2;margin-right:20px;display:inline-block"></span>
<span style="margin-right:20px;margin-bottom:10px;">General Labor</span>
<span style="width:20px;height:20px;background:#E67E22;border:1px solid #E67E22;margin-right:20px;display:inline-block"></span>
<span style="margin-right:20px;margin-bottom:10px;">Packing</span>
</div>
<div id="visualization"></div>

<script type="text/javascript">
var groups = new vis.DataSet([
  <?php
  for($q=0;$q<$actempc;$q++){
	echo '{"content": "'.$employee[$q]['name'].'", "id": "'.$employee[$q]['name'].'", "value": 1, className:"'.$employee[$q]['dept'].'"},';
}
   ?>
	
  ]);
  // DOM element where the Timeline will be attached
  var container = document.getElementById('visualization');

  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
   <?php
   $qid=1;
   for($p=0;$p<$oi;$p++){
for($i=0;$i<$tfc;$i++){
  $tfmark = 2;
  if($oc[$p]['NAME']==$timeoffa[$i]['name']){
    if(strtotime($oc[$p]['date'])>=strtotime($timeoffa[$i]['start'])&&strtotime($oc[$p]['date'])<=strtotime($timeoffa[$i]['end'])){
echo '{id: '.$qid.', content: "", group: "'.$oc[$p]['NAME'].'", start: "'.$oc[$p]['date'].'T'.$oc[$p]['start'].':00.000Z", end: "'.$oc[$p]['date'].'T'.$oc[$p]['end'].':00.000Z", style:\'border-color:'.$oc[$p]['color'].';border-style:dashed;opacity:0.7;border-width: 3px;background-color:white\' },';
$tfmark=1;
break;
}
}
}
if($tfmark == 2)
echo '{id: '.$qid.', content: "", group: "'.$oc[$p]['NAME'].'", start: "'.$oc[$p]['date'].'T'.$oc[$p]['start'].':00.000Z", end: "'.$oc[$p]['date'].'T'.$oc[$p]['end'].':00.000Z", style:\'border-color:'.$oc[$p]['color'].';border-width: 3px;background-color:white\' },';
//border-style:dashed;
//echo '{id: 999999, content: "", group: "'.$oc[$p]['NAME'].'", start: "'.$oc[$p]['date'].'T'.$oc[$p]['start'].':00.000Z", end: "'.$oc[$p]['date'].'T'.$oc[$p]['end'].':00.000Z", style:\'border-width: 0px;background-color:'.$oc[$p]['color'].'\' },';
$qid++;
//$color = $color1;
 }
 $qids=5000;
 for($p=0;$p<$si;$p++){
   
 echo '{id: '.$qids.', content: "", group: "'.$opp[$p]['name'].'", start: "'.$opp[$p]['timestampsta'].'", end: "'.$opp[$p]['timestampend'].'", style:\'background-color:'.$opp[$p]['color'].';opacity:0.6;z-index:999;border-color:'.$opp[$p]['color'].';border-width: 3px; \' },';
$qids++; 
}

  ?>
  ]);

  // Configuration for the Timeline
  var options = {
    hiddenDates: [
      {start: '2018-10-20 14:00:00', end: '2018-10-20 21:00:00', repeat: 'daily'}
          //{start: '2018-06-10 04:00:00', end: '2018-06-10 20:00:00', repeat: 'daily'} // daily weekly monthly yearly
        ],
        //start: '2018-06-17',
        //end: '2018-07-01',
    showCurrentTime:false ,
    orientation:'top',
    groupOrder: 'className',
    stackSubgroups: true,
    stack: false,
    moment: function(date) {
    return vis.moment(date).utc();
  }
  };

  // Create a Timeline
  
  var timeline = new vis.Timeline(container);
  timeline.setOptions(options);
  timeline.setGroups(groups);
  timeline.setItems(items);
</script>
<?php
//for($p=4;$p<20;$p++){
//echo '{id: '.$qid.', content: "", group: "'.$ob[$p]['NAME'].'", start: "'.$date1.'T'.$ob[$p]['B'].':00.000Z", end: "'.$date1.'T'.$ob[$p+1]['B'].':00.000Z", style:\'background-color:'.$color.'\' },';
//}
?>

</body>
</html>

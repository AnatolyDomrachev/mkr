<?php

namespace Application\Model;

class File
{

public function createArray($handle)
{
$pattern='/^ \)/';
while (!preg_match($pattern, $buffer)){
	$buffer = fgets($handle);
	////echo $buffer;
	$array_tmp=explode('/', $buffer);
	unset($array_tmp[count($array_tmp)-1]);
	foreach($array_tmp as $str){
			$array[]=explode(' ', $str);	
	}
	//unset($array[count($array)-1]);
	//echo 0;
	//print_r($array_tmp);
	
}
return $array;
}
public function findStr($handle,$pattern)
{
if ($handle) {
    while (($buffer = fgets($handle,12)) !== false) {
        if(preg_match($pattern, $buffer))
			return $handle;
    }

    if (!feof($handle)) {
        //echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

else
	echo "<pre>\n error open file";
}
//echo "<pre>";

public function getArray($file)
{
//echo "<pre>";
//print_r($file);
//die();

$handle = @fopen($file, "r");
$patternLine = array('str'=>'/\( 1/');
$patternPoint = array('str'=>'/\( 4/');
$patternHand = array('str'=>'/\( 5/');

/* $handle=$this->findStr($handle,$patternLine['str']);

$lines_tmp=$this->createArray($handle) */;
/* unset($lines_tmp_tmp[0]);
foreach($lines_tmp_tmp as $line)
$lines_tmp[]=preg_split('/ /', $line); */

$handle=$this->findStr($handle,$patternPoint['str']);
$points_tmp=$this->createArray($handle);

/* $handle=$this->findStr($handle,$patternHand['str']);

//массив закреплений
$pattern='/^ \)/';
while (!preg_match($pattern, $buffer)){
	$buffer = fgets($handle);
	$hands_tmp[]=explode(' ',$buffer);
}
unset($hands_tmp[count($hands_tmp)-1]);
unset($hands_tmp[count($hands_tmp)-1]);

//$mashtab=max(max($points_tmp));
$mashtab=1; */



foreach($points_tmp as $p){
	$pointsx[]=$p[0];
	$pointsy[]=$p[1];
	$pointsz[]=$p[2];
}
$gabarit = array('xmin'=>min($pointsx),'xmax'=>max($pointsx),'ymin'=>min($pointsy),'ymax'=>max($pointsy),
				'zmin'=>min($pointsz),'zmax'=>max($pointsz));
//центр
$points['cx']=($gabarit['xmin']+$gabarit['xmax'])/2;
$points['cy']=($gabarit['ymin']+$gabarit['ymax'])/2;
$points['cz']=($gabarit['zmin']+$gabarit['zmax'])/2;
$points['dx']=abs($gabarit['xmin']-$gabarit['xmax'])/2;
$points['dy']=abs($gabarit['ymin']-$gabarit['ymax'])/2;
$points['dz']=abs($gabarit['zmin']-$gabarit['zmax'])/2;


/* foreach($lines_tmp as $p){
	$lines[]=array($p[2]-1,$p[3]-1);
}

foreach($hands_tmp as $p){
	$hands[]=$p[0]-1;
}

//координаты кубов закреплений
$handsize=$mashtab/800;
$h=$handsize;
$i=0; */
/* foreach($hands as $hs){
	//foreach($points["$hs"] as $р){
		$hCoords[]=array('number'=>$i,'coords'=>array(		
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
// задняя часть
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
//левая боковая часть
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
// правая боковая часть
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
// низ
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']- $h  , $points["$hs"]['z']+ $h  ,
// верх
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']- $h  ,
 $points["$hs"]['x']+ $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
 $points["$hs"]['x']- $h  , $points["$hs"]['y']+ $h  , $points["$hs"]['z']+ $h  ,
		));
	$i++;
}	 */



//$Array=array('lines'=>$lines,'points'=>$gabarit,'hands'=>$hCoords,'mashtab'=>$mashtab,'handsize'=>$handsize);
$Array=array('points'=>$points);
return $Array;
/* //echo 1;
print_r($lines);
//echo 2;
print_r($points);
//echo 3;
print_r($hands); */
}
}
?>

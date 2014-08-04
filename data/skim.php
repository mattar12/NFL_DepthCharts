<?php
function get_all_string_between($string, $start, $end)
{
    $result = array();
    $string = " ".$string;
    $offset = 0;
    while(true)
    {
        $ini = strpos($string,$start,$offset);
        if ($ini == 0)
            break;
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        $result = substr($string,$ini,$len);
        $offset = $ini+$len;
    }
    return $result;
    /*    if (count($result)>0){
            return $result;
        } else {
            return null;
        }*/
}

$allhtml = file_get_contents('http://subscribers.footballguys.com/apps/depthchart.php?type=skill&lite=no&exclude_coaches=yes');

$main = explode('</form>',$allhtml);
$newMain = str_replace('</table>', '</table> <hr id="split">', $main[4]);
$rows = explode('<hr id="split">',$newMain);
// echo $newMain;
$cleanRows = strip_tags($newMain, '<tr><td>');
$cleanRows = str_replace('[Back to top]','',$cleanRows);
$cleanRows = str_replace('<tr valign="top">','',$cleanRows);
$cleanRows = str_replace('<td class="la" width="50%">','',$cleanRows);
$cleanRows = str_replace('  ',' ',$cleanRows);
$cleanRows = str_replace("\n",'',$cleanRows);
$cleanRows = str_replace(' <','<',$cleanRows);
$cleanRows = str_replace('&#8242;','',$cleanRows);
$cleanRows = str_replace('.','',$cleanRows);
$cleanRows = str_replace('<tr><td>',"\n\n<team>\n<teamName>",$cleanRows);

$cleanRows = str_replace('</td></tr><tr><td class="la">',"</teamName>\n<depthChart>",$cleanRows);
$cleanRows = str_replace('</td></tr>',"</depthChart>\n</team>",$cleanRows);

$cleanRows = explode('DEPTH CHART KEY',$cleanRows);
$cleanRows = $cleanRows[0];
$cleanRows= rtrim($cleanRows," ");
$cleanRows= rtrim($cleanRows,"</team>");
$cleanRows= rtrim($cleanRows,"</depthChart>\n");
$cleanRows .= '>';

$pattern = "#<\s*?team\b[^>]*>(.*?)</team\b[^>]*>#s";
$cleanRows = str_replace('<team>',"******\n<team>",$cleanRows);
$cleanRows = str_replace("\n******",'******',$cleanRows);
$cleanRows = str_replace("\n******",'******',$cleanRows);
$cleanRows = str_replace("******\n",'******',$cleanRows);
$cleanRows = str_replace("QB: ",'<QB>',$cleanRows);
$cleanRows = str_replace("RB: ",'</QB><RB>',$cleanRows);
$cleanRows = str_replace("FB: ",'</RB><FB>',$cleanRows);
$cleanRows = str_replace("WR: ",'</FB><WR>',$cleanRows);
$cleanRows = str_replace("TE: ",'</WR><TE>',$cleanRows);
$cleanRows = str_replace("</depthChart>",'</TE></depthChart>',$cleanRows);
$teams = explode('******',$cleanRows);

//echo $cleanRows; // Final string with all position tags ready to create arrays

function parseTag($str,$tag){
    $regex_pattern = "'<$tag>(.*?)</$tag>'si";
    preg_match_all($regex_pattern,$str,$matches);
    if(!isset($matches[0][0])){
        $matches[0][0] = ''; //if position is empty ensure empty string is still returned
    } else {
        return $matches[0][0];
    }
}

foreach($teams as $tm){ // split string into associative array with team name as key...and some other things
    $findFB = strpos($tm,'<FB>'); 
    if($findFB==0){ // if team has no FB, position tags will need fixed
        $tm = str_replace("</FB>",'</RB>',$tm);
    }

    $tmName = parseTag($tm,'teamName'); 
    $qbs = parseTag($tm,'QB'); 
    $rbs = parseTag($tm,'RB');
    $fbs = parseTag($tm,'FB');
    $wrs = parseTag($tm,'WR');
    $tes = parseTag($tm,'TE');
    if(strlen($tmName)<3){
        continue;
    }
    if(str_word_count($tmName)>1){ // make all team names = mascot name only
        $words= explode(' ', $tmName);
        $tmName= array_pop($words);
    }
    $TeamNames[]=$tmName;
    $depthCharts[$tmName]= array(
        "QB"=>explode(', ', $qbs),
        "RB"=>explode(', ', $rbs),
        "FB"=>explode(', ', $fbs),
        "WR"=>explode(', ', $wrs),
        "TE"=>explode(', ', $tes)
    );
}
ksort($depthCharts); // make order of array sorted by key/team name 
$output["DepthCharts"]=$depthCharts;
$output["Teams"]=$TeamNames;
$output["Positions"]=array("QB","RB","FB","WR","TE");
$output["LastUpdated"]=date('m-d-Y');
$output = preg_replace("/<[^>]*>/",'',json_encode($output));

echo $output; // echo the json

$file = 'depthCharts.json';

$fh = fopen($file, 'a');
unlink($file); //This deletes the existing depthCharts.json file. Requires appropriate server privileges...I think I used 755
fclose($fh);

$fh = fopen($file, 'a');
fwrite($fh, $output);
fclose($fh);

// uncomment to output a clean tree of the array...only useful for testing purposes.
/*
echo "<pre>";
print_r($output);
echo "</pre>";
*/
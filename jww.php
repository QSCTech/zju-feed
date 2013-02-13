<?php
$html = file_get_contents('http://jwbinfosys.zju.edu.cn/default2.aspx');
$html = @iconv('GBK', 'UTF-8//ignore', $html);
$items = explode('<td align="Left">', $html);
$rss_items = array();
$time_array = array();
array_shift($items);
foreach($items as $item) {
    list($title, $author, $time) = explode('</td', $item, 3);
    if($time) {
        list($link, $title) = explode('<img src=images/tzgg_icon.gif border=0/>  ', $title, 2);
        $link = str_replace(array('<a href="#" onclick="window.open(\'', '\',\'gxlb\',\'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=400,left=120,top=60\')">'), '', $link);
        $title = str_replace('</a>', '', $title);
        $author = str_replace(array('<td>','>'), '', $author);
        $time = str_replace(array('<td>', '>'), '', $time);
        list($time, $nil) = explode('</td', $time, 2);
        if($title) {
            $time = new DateTime($time);
            $time_array[] = $time;
            $time = $time->format(DateTime::ATOM);
            array_push($rss_items, array('link'=>$link,'title'=>$title,'author'=>$author,'time'=>$time));
        }
    }
 }

include 'class.atomgen.php';
$atom = new AtomGen('浙大教务网通知', 'http://jwbinfosys.zju.edu.cn/', '浙江大学本科生院');
foreach($rss_items as $item)
    {
        $atom->add($item['title'], $item['link'], $item['time'], $item['title']);
    }
$atom->display();
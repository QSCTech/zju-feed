<?php
// Copyright (C) 2013 Zeno Zeng
// Time-stamp: <2013-02-13 20:06:54 Zeno Zeng>
// Licensed under the MIT license
class AtomGen {

    private $_items;
    private $_title;
    private $_link;
    private $_author;

    public function __construct($title, $link, $author, $items = array())
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->_title = $title;
        $this->_link = $link;
        $this->_author = $author;
        $this->_items = $items;
    }

    private static function wellFormUrl($url)
    {
        $items = explode('/', $url);
        foreach($items as &$item) {
            $item = rawurlencode($item);
        }
        $url = implode('/', $items);
        $url = str_replace(array('http%3A', 'https%3A'), array('http:', 'https:'), $url);
        return $url;
    }

    private static function uuid($input)
    {
        $chars = md5($input);
        $uuid  = substr($chars,0,8) . '-';
        $uuid .= substr($chars,8,4) . '-';
        $uuid .= substr($chars,12,4) . '-';
        $uuid .= substr($chars,16,4) . '-';
        $uuid .= substr($chars,20,12);
        return 'urn:uuid:' . $uuid;
    }

    private function lastestUpdateTime()
    {
        $lastest = new DateTime('2000-01-01 00:00');
        foreach ($this->_items as $v) {
            $v = $v['time'];
            if($v > $lastest)
                $lastest = $v;
        }
        return $lastest->format(DateTime::ATOM);
    }

    public function add($title, $link, $time, $summary)
    {
        $time = new DateTime($time);
        $link = $this->wellFormUrl($link);
        array_push($this->_items, array('title'=>$title, 'link'=>$link, 'time'=>$time, 'summary'=>$summary));
    }

    public function gen()
    {
        $atom = '<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom">';
        $uuid = $this->uuid($this->_title.$this->_link);
        $atom .= '<title>'.$this->_title.'</title>';
        $atom .= '<link>'.$this->_link.'</link>';
        $atom .= '<id>'.$uuid.'</id>';
        $atom .= '<updated>'.$this->lastestUpdateTime().'</updated>';
        $atom .= '<author><name>'.$this->_author.'</name></author>';
        foreach($this->_items as $item)
            {
                $atom .= '<entry>';
                $atom .= '<title>'.$item['title'].'</title>';
                $atom .= '<link href="'.$item['link'].'" />';
                $atom .= '<id>'.$this->uuid($item['link'].$item['title']).'</id>';
                $atom .= '<updated>'.$item['time']->format(DateTime::ATOM).'</updated>';
                $atom .= '<summary>'.$item['title'].'</summary>';
                $atom .= '</entry>';
            }
        $atom .= '</feed>';
        return $atom;
    }

    public function display()
    {
        header("Content-Type:application/xml");
        echo $this->gen();
    }
        
        
}
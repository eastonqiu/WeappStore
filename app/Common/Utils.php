<?php

namespace App\Common;

class Utils {
    public static function timeForHuman($seconds) {
        $minutes = floor($seconds / 60);
    	$seconds = $seconds - $minutes * 60;
    	$hours   = floor($minutes / 60);
    	$minutes = $minutes - $hours * 60;
    	$days    = floor($hours / 24);
    	$hours = $hours - $days * 24;

    	$humanTime = '';
    	if ($days > 0) {
    		$humanTime .= $days . '天';
    	}
    	if($hours > 0) {
    		$humanTime .= $hours . '小时';
    	}
    	if($minutes > 0) {
    		$humanTime .= $minutes . '分';
    	}
    	if($seconds > 0) {
    		$humanTime .= $seconds . '秒';
    	}

    	if(empty($humanTime))
    		return '0秒';
    	return $humanTime;
    }
}

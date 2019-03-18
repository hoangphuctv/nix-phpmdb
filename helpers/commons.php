<?php

function init($config){
	$post      = POST;
	$cache     = CACHE;
	$all_cache = "$cache/all";
	if ($config->cache == 'off' || !file_exists($all_cache)) {
		$fs = file_find($post);
		$fs = array_filter($fs, function($f){ return preg_match("/\.md$/", $f, $m); });
		sort($fs);
		$fs = array_reverse($fs);
		$fs = implode(PHP_EOL, $fs);
		$fs = str_replace(POST, './', $fs);
		file_put_contents($all_cache, $fs);
	}
}
	
function parse_uri(){
	$uri = str_replace('..', '', explode("?", $_SERVER['REQUEST_URI'])[0]);
	$uri = str_replace('.html', '.md', $uri);
	return $uri;
}

function find_posts($offset, $limit){
	$head = $offset + $limit;

	$all_cache = CACHE."/all";
	
	$posts = `head -n $head $all_cache | tail -n $limit`;
	$posts = explode("\n", trim($posts));

	$total = intval(`wc -l $all_cache`);
	return [$posts, $total];
}



function parse_post($post_path){

	$post_path = str_replace("./", '/', $post_path);
	$post = [
		'title'    => get_post_title($post_path),
		'description'    => get_post_description($post_path),
		'name'     => basename($post_path),
		'path'     => ltrim($post_path, "/"),
		'fullpath' => POST . '/' . $post_path,
	];
	$post['mod_date'] = full_date(filemtime($post['fullpath']));
	$post['create_date'] = full_date(filectime($post['fullpath']));
	if (empty($post['mod_date'])) {
		$post['mod_date'] = $post['create_date'];
	}
	return $post;
}

function get_post_title($post_path) {
	$file = POST.$post_path;
	$t = file_head($file, 1);
	// $t = `head "$file" -n 1`; var_dump($t);die;
	$t = trim($t);
	if (empty($t)) {
		$t = str_replace(".md", ".html", $post_path);
	}else{
		$t = preg_replace("/\#\s*/", '', $t);
	}
	return $t;
}

function get_post_description($post_path) {
	$file = POST.$post_path;
	$t = `tail -n +2 $file | tr ' ' '\n' | head -30 | tr '\n' ' '`;
	return $t;
}


function get_next_posts($current_post, $n=1) {
	$all = CACHE ."/all";
	$posts = `awk '$0 == ".$current_post" {i=1;next};i && i++ <= $n' $all`;
	$posts = trim($posts);
	if (empty($posts)) {
		$posts = `tail -n $n $all`;
		$posts = trim($posts);
	}
	return explode("\n", $posts);
}

function short_date($time) {
	global $config;
	$format = isset($config->short_date) ? $config->short_date : "Y-m-d";
	return date ($format, $time);
} 

function full_date($time) {
	global $config;
	$format = isset($config->full_date) ? $config->full_date : "Y-m-d H:i:s";

	$today = date("Y-m-d");
	$date = str_replace($today, '', date ($format, $time));
	return $date;
} 


function current_url() {
    $protocol = 'http';
    if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
        $protocol .= 's';
        $protocol_port = $_SERVER['SERVER_PORT'];
    } else {
        $protocol_port = 80;
    }

    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'];
    $request = $_SERVER['REQUEST_URI'];
    $query = isset($_SERVER['argv']) ? substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1) : '';

    $toret = $protocol . '://' . $host . $request . (empty($query) ? '' : '?' . $query);
    return $toret;
}

function file_head($filename, $n=1){
	$lines = [];

	$handle = fopen($filename, "r");
	if (!$handle) {
		return false;
	}
	$i = 0;
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $lines[] = $buffer;
        $i++;
        if ($i >= $n) {
        	break;
        }
    }
    fclose($handle);
    return implode(PHP_EOL, $lines);
}

function file_each_line($filename, $callback) {
	$handle = fopen($filename, "r");
	if (!$handle) {
		return false;
	}
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $callback($buffer);
    }
    fclose($handle);
}

function file_find($dir, &$results=[], $file=true) {
	$files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            file_find($path, $results);
            if ($file == false) {
	            $results[] = $path;
            }
        }
    }

    return $results;
}


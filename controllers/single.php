<?php

$content = file_get_contents($mdfile);

$parsedown = new Parsedown();
$post = $parsedown->text($content);

$posts = get_next_posts($md_origin, $config->post_relate);

$ps = [];
foreach($posts as $p) {
	$ps[] = (object) parse_post($p);
}
$posts = $ps;

include VIEW."/single.php";

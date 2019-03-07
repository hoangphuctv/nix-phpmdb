<h4><small><?= isset($sub_title) ? $sub_title : "Recent updates" ?></small></h4>
<hr>
<?php if(isset($posts)) foreach($posts as $p){ ?>
<h2><a href="<?= str_replace('.md', '.html', $p->path) ?>"><?= htmlspecialchars($p->title) ?></a></h2>
<h5><span class="glyphicon glyphicon-time"></span> <?= $p->mod_date ?></h5>
<p><?= htmlspecialchars($p->description) ?>...</p>
<br><br>
<?php } ?>
<hr>
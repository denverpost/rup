<?php

$filename=$_GET['filename'];

header("Content-disposition: attachment; filename={$filename}.html");
header("Content-type: text/html");
readfile('./cache/'."$filename".".html");
?>
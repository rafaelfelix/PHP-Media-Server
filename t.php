<?php

require(__DIR__.'/lib/RedisServer/RedisServer.php');

$conn = new RedisServer;

//$conn->Set('foo', 'bar');
//$conn->Set('foo2', 'bar2');
//$conn->Set('foo3', 'bar3');
//$conn->Set('foo4', 'bar4');

//echo $conn->Get('foo');

//print_r($conn->Keys('*'));



$img = base64_encode(file_get_contents('/tmp/img.jpg'));

echo $conn->Set('testeimg', $img);
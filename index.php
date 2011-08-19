<?php
/**
 * PHP Media Server configuration file
 */

require(__DIR__.'/config.php');
require(__DIR__.'/lib/RedisServer/RedisServer.php');


// Valida URI recebida
$out = array();
preg_match_all("/\/img\/([c||b])\/([0-9]+)x([0-9]+)\/([A-Za-z0-9]+).jpg/", PHPMS_REQUEST_URI, $out);

// Parse dos parâmetros
list($color, $width, $height, $hash) = array($out[1][0], $out[2][0], $out[3][0], $out[4][0]);

$redis = new RedisServer;

$redis_img = $redis->Get($hash);

if ($redis_img) {
    
    $x = $width;
    $y = $height;
    $image_dest = imagecreatetruecolor($width, $height);
    $image_orig = imagecreatefromstring(base64_decode($redis_img));

    // Caso seja zerado o width e height, não precisa cropar
    if($width > 0 && $height > 0){
        imagecopyresampled($image_dest, $image_orig, 0, 0, 0, 0, $x+1, $y+1, imagesx($image_orig), imagesy($image_orig));		
    }else{
        $image_dest = $image_orig;
    }	

    // Aplicando filtro de cor
    if ($color == 'b') { // Preto e branco
        imagefilter($image_dest, IMG_FILTER_GRAYSCALE);
    }

    //salvando o conteúdo da imagem em variável
    ob_start();
    imagejpeg($image_dest, null, 80);
    $img_final = ob_get_contents();
    ob_end_clean();

    // Envia a imagem modificada
    header('Content-type: '.PHPMS_CONTENT_TYPE);
    header('ETag '.md5($img_final));
    echo $img_final;

    // Libera memória
    imagedestroy($image_orig);
    imagedestroy($image_dest);

} else {
    // Não encontrou nada
    header('HTTP/1.0 404 Not Found', true, 404);
}

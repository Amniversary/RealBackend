<?php
namespace common\components\mp3;

function mp3Time($file) {
    $m = new mp3file($file);
    $a = $m->get_metadata();
    return $a['Length mm:ss'] ? $a['Length mm:ss'] : 0;
}

function mp3Info($file) {
    $m = new mp3file($file);
    return $m->get_metadata();
}

$_time = mp3Time('3.mp3');

echo '<meta charset="UTF-8">';
echo "歌曲时间长：".$_time.'<br />';


$_info = mp3Info('3.mp3');
print_r($_info);
?>

<?php
    $filePath = './kadai_6.txt';
    $array = file($filePath);

    echo implode(' ', $array);
?>
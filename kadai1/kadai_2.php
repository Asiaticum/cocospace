<?php
    touch('./test.txt');
    chmod('./test.txt', 0666);
    $fileName = './test.txt';

    file_put_contents($fileName, '課題2用追記テキスト');
    echo 'Done';

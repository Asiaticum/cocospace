<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        名前：<input type="text" name="name"> <br>
        コメント：<input type="text" name="comment"> <br>
        <input type="submit" value="送信">
        <?php
            $postNumber_file = './post_number_2_2.txt';

            if (!empty($_POST["name"])) {
                $filePath = './name_list_2_2.txt';
                $name = $_POST["name"];
                $date = date('Y-m-d');
                $comment = $_POST["comment"];
                // 番号管理のためのファイルからと現在の投稿番号を読み込む．
                $postNumber = (int)file($postNumber_file)[0] + 1;

                if (!file_exists($filePath)) {
                    touch($filePath);
                    chmod($filePath, 0666);
                    file_put_contents($filePath, $postNumber."<>".$name.'<>'.$comment."<>".$date."\n");
                } else {
                    file_put_contents($filePath, $postNumber."<>".$name.'<>'.$comment."<>".$date."\n", FILE_APPEND);
                }

                file_put_contents($postNumber_file, $postNumber);
            }
        ?>
    </form>
</body>
</html>
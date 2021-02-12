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
        削除番号：<input type="text" name="delete"> <br>
        <input type="submit" value="送信"> <br>
        <?php
            $nameFilePath = './name_list_2_4.txt';
            $postNumberFile = './post_number_2_4.txt';
            if (!file_exists($nameFilePath)) {
                touch($nameFilePath);
                chmod($nameFilePath, 0666);
            }

            // 名前フォームが空の時には処理を行わない(一番最初にページを読み込んだ時にエラーが出るのを防ぐため)
            if (!empty($_POST["name"])) {
                $name = $_POST["name"];
                $date = date('Y-m-d');
                $comment = $_POST["comment"];
                // 番号管理のためのファイルからと現在の投稿番号を読み込む．
                $postNumber = (int)file($postNumberFile)[0] + 1;

                file_put_contents($nameFilePath, $postNumber."<>".$name.'<>'.$comment."<>".$date."\n", FILE_APPEND);
                file_put_contents($postNumberFile, $postNumber);
            }

            // 削除番号フォームが入力されたときのみ動作
            if (!empty($_POST["delete"])) {
                $nameList = file($nameFilePath);

                // ファイルを一度白紙にする
                file_put_contents($nameFilePath, '');
                foreach($nameList as $name){
                    $number = explode('<>', $name)[0];

                    // 削除番号と投稿番号が一致するときのみnameListファイルへの追記をしない
                    if (strcmp($number, $_POST["delete"]) != 0) {
                        file_put_contents($nameFilePath, $name, FILE_APPEND);
                    }
                }
            }

            // 名前の一覧をフォームの下に表示する．
            $nameList = file($nameFilePath);
            foreach($nameList as $name){
                $infos = explode("<>", $name);
                foreach($infos as $info){
                    echo $info.' ';
                }
                echo '<br>';
            }
        ?>
    </form>
</body>
</html>
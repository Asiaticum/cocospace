<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php // 編集番号フォームが入力された場合に名前とコメントフォームに既存の値を入力するための処理
        $nameFilePath = './name_list_2_5.txt';
        $postNumberFile = './post_number_2_5.txt';
        if (!file_exists($nameFilePath)) {
            touch($nameFilePath);
            chmod($nameFilePath, 0666);
        }
        $nameList = file($nameFilePath);

        if (!empty($_POST["edit"])) {
            foreach($nameList as $info){
                $infos = explode('<>', $info);
                $number = $infos[0];
                // 編集番号と投稿番号が一致した時に編集したい名前とコメントを取得
                if (strcmp($number, $_POST["edit"]) == 0) {
                    $presentName = $infos[1];
                    $presentComment = $infos[2];
                    break 1;
                }
            }
        }
    ?>
    <form action="" method="post">
        名前：<input type="text" name="name", value="<?php if (!empty($presentName)) {echo $presentName;}?>"> <br>
        コメント：<input type="text" name="comment", value="<?php if (!empty($presentComment)) {echo $presentComment;}?>"> <br>
        削除番号：<input type="text" name="delete"> <br>
        編集番号：<input type="text", name="edit"> <input type="hidden" name="edit_No", value="<?php if(!empty($_POST["edit"])){echo $_POST["edit"];} ?>"> <br>
        <input type="submit" value="送信"> <br>
        <?php
            $date = date('Y-m-d');

            // 名前フォームが空の時には処理を行わない(一番最初にページを読み込んだ時にエラーが出るのを防ぐため)
            // 編集モードがONの時には動作しない．
            if (!empty($_POST["name"]) && empty($_POST["edit_No"])) {
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                // 番号管理のためのファイルからと現在の投稿番号を読み込む．
                $postNumber = (int)file($postNumberFile)[0] + 1;

                file_put_contents($nameFilePath, $postNumber."<>".$name.'<>'.$comment."<>".$date."\n", FILE_APPEND);
                file_put_contents($postNumberFile, $postNumber);
            }

            // 編集モードで入力フォームが送られたときの動作
            if (!empty($_POST["edit_No"])) {
                file_put_contents($nameFilePath, '');
                foreach($nameList as $info){
                    $infos = explode('<>', $info);
                    $number = $infos[0];
                    $name = $infos[1];
                    $comment = $infos[2];

                    if (strcmp($number, $_POST["edit_No"]) == 0) {
                        $name = $_POST["name"];
                        $comment = $_POST["comment"];
                        file_put_contents($nameFilePath, $number."<>".$name."<>".$comment."<>".$date."\n", FILE_APPEND);
                    } else {
                        file_put_contents($nameFilePath, $info, FILE_APPEND);
                    }
                }
            }

            // 削除番号フォームが入力されたときのみ動作
            if (!empty($_POST["delete"])) {

                // ファイルを一度白紙にする
                file_put_contents($nameFilePath, '');
                foreach($nameList as $info){
                    $number = explode('<>', $info)[0];

                    // 削除番号と投稿番号が一致するときのみnameListファイルへの追記をしない
                    if (strcmp($number, $_POST["delete"]) != 0) {
                        file_put_contents($nameFilePath, $info, FILE_APPEND);
                    }
                }
            }

            // 名前の一覧をフォームの下に表示する．
            $nameList = file($nameFilePath);  // delete操作などが行われたときのためにファイルを再度読み込む．
            foreach($nameList as $info){
                $infos = explode("<>", $info);
                foreach($infos as $info){
                    echo $info.' ';
                }
                echo '<br>';
            }
        ?>
    </form>
</body>
</html>
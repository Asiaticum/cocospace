<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <?php
        // パスワードが合っているかをチェック
        function passwordCheck($nameList, $editNumber, $conformationPassword){
            $flag = FALSE;
            foreach($nameList as $info){
                $info = str_replace(PHP_EOL, '', $info);  // 文字列末尾の改行コードの除去．改行コードが含まれるとパスワードの判定が正しく行われない．
                $infos = explode("<>", $info);
                $number = $infos[0];
                $password = end($infos);
                if (strcmp($number, $editNumber) == 0 && strcmp($password, $conformationPassword) == 0){
                    $flag = TRUE;
                }
            }
            return $flag;
        }

        // 削除番号・編集番号で入力した番号が投稿一覧の中にあるかチェック
        function numberCheck($nameList, $editNumber) {
            $flag = FALSE;
            foreach($nameList as $info){
                $number = explode("<>", $info)[0];
                if (strcmp($number, $editNumber) == 0){
                    $flag = TRUE;
                }
            }
            return $flag;
        }
    ?>
</head>

<body>
    <?php // 編集番号フォームが入力された場合に名前とコメントフォームに既存の値を入力するための処理
        $nameFilePath = './name_list_2_6.txt';
        $postNumberFile = './post_number_2_6.txt';
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
        名前：<input type="text" name="name", value="<?php if(!empty($presentName)){echo $presentName;}?>"> <br>
        コメント：<input type="text" name="comment", value="<?php if(!empty($presentName)){echo $presentComment;}?>"> <br>
        パスワード：<input type="text" name="password"> <br>
        削除番号：<input type="text" name="delete"> <input type="hidden" name="delete_No" value="<?php if(!empty($_POST["delete"])){echo $_POST["delete"];}?>"> <br>
        編集番号：<input type="text", name="edit"> <input type="hidden" name="edit_No", value="<?php if(!empty($_POST["edit"])){echo $_POST["edit"];} ?>"> <br>
        <?php
            if (!empty($_POST["delete"]) || !empty($_POST["edit"])) {
                // 削除番号・編集番号に入力した番号がリスト一覧の中にある場合はパスワードの確認に進む．
                if (numberCheck($nameList, $_POST["delete"]) || numberCheck($nameList, $_POST["edit"])) {
                    echo "確認のため以下にパスワードを入力して送信ボタンを押してください．<br> <input type='text' name='conformation_password'> <br>";
                    echo "<button type='submit'>送信</button> <button type='button' onclick='history.back()'>キャンセル</button> <br>";
                } else {
                    echo "入力された番号は無効です．<br>";
                    echo "<button type='submit'>送信</button> <br>";
                }
            } else {
                echo "<button type='submit'>送信</button> <br>";
            }
        ?>
        <?php
            $date = date('Y-m-d');

            // 名前フォームが空の時には処理を行わない(一番最初にページを読み込んだ時にエラーが出るのを防ぐため)
            // 編集モードがONの時には動作しない．
            if (!empty($_POST["name"]) && empty($_POST["edit_No"])) {
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $password = $_POST["password"];
                // 番号管理のためのファイルからと現在の投稿番号を読み込む．
                $postNumber = (int)file($postNumberFile)[0] + 1;

                file_put_contents($nameFilePath, $postNumber."<>".$name.'<>'.$comment."<>".$date."<>".$password."\n", FILE_APPEND);
                file_put_contents($postNumberFile, $postNumber);
            }

            // 編集モードで入力フォームが送られたときの動作
            if (!empty($_POST["edit_No"])) {
                // パスワードが合っているか確認をする時に使用．
                $isPasswordOk = passwordCheck($nameList=$nameList, $editNumber=$_POST["edit_No"], $conformationPassword=$_POST["conformation_password"]);

                if ($isPasswordOk) {
                    file_put_contents($nameFilePath, '');
                    foreach($nameList as $info){
                        $info = str_replace(PHP_EOL, '', $info);  // 文字列末尾の改行コードの除去．これがないとパスワードの判定が正しく行われない．
                        $infos = explode('<>', $info);
                        $number = $infos[0];
                        $name = $infos[1];
                        $comment = $infos[2];
                        $password = $infos[4];

                        if (strcmp($number, $_POST["edit_No"]) == 0) {
                            $name = $_POST["name"];
                            $comment = $_POST["comment"];
                            file_put_contents($nameFilePath, $number."<>".$name."<>".$comment."<>".$date."<>".$password."\n", FILE_APPEND);
                        } else {
                            file_put_contents($nameFilePath, $info."\n", FILE_APPEND);
                        }
                    }
                } else {
                    echo "パスワードが間違っています．再度編集番号から入力しなおしてください．<br>";
                }
            }
            // 編集モードの処理終了

            // 削除番号モードでの動作
            if (!empty($_POST["delete_No"])) {
                // パスワードが合っているか確認をする時に使用．
                $isPasswordOk = passwordCheck($nameList=$nameList, $editNumber=$_POST["delete_No"], $conformationPassword=$_POST["conformation_password"]);

                if ($isPasswordOk) {
                    // ファイルを一度白紙にする
                    file_put_contents($nameFilePath, '');
                    foreach($nameList as $info){
                        $number = explode('<>', $info)[0];

                        // 削除番号と投稿番号が一致するときのみnameListファイルへの追記をしない
                        if (strcmp($number, $_POST["delete_No"]) != 0) {
                            file_put_contents($nameFilePath, $info, FILE_APPEND);
                        }
                    }
                    echo $_POST["delete_No"]."番の内容を削除しました．<br>";
                } else {
                    echo "パスワードが間違っています．再度削除番号から入力しなおしてください．<br>";
                }
            }
            // 削除モードの処理終了

            // 名前の一覧をフォームの下に表示する．
            $nameList = file($nameFilePath);  // delete操作などが行われたときのためにファイルを再度読み込む．
            foreach($nameList as $info){
                $infos = explode("<>", $info);
                for ($i = 0; $i < count($infos) - 1; $i++) {
                    echo $infos[$i].' ';
                }
                echo '<br>';
            }
        ?>
    </form>
</body>
</html>
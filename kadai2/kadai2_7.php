<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <?php
        // MySQLへの接続
        $host = '';
        $username = '';
        $pw = '';
        $dbname = '';
        $link = mysqli_connect($host, $username, $pw, $dbname);
        // テーブルが作成されていなければ作成する．
        $makeTable = "create table if not exists bbs(
            id int not null auto_increment primary key,
            name varchar(255) not null,
            comment varchar(255),
            time_posted timestamp not null default current_timestamp,
            password varchar(255) not null
        )";
        mysqli_query($link, $makeTable);

        // 削除番号・編集番号で入力した番号が投稿番号の中に存在するかを確認する関数
        function checkId($link, $post) {
            $checkQuery = "select * from bbs where id=".mysqli_real_escape_string($link, $post);
            $res = mysqli_query($link, $checkQuery);
            $flag = mysqli_num_rows($res);
            return $flag;
        }
    ?>
</head>

<body>
    <?php
        // 編集番号フォームが入力された場合に名前とコメントフォームに既存の値を入力するための処理
        // 編集番号と削除番号が同時に入力された場合は削除操作の方を優先
        if (!empty($_POST["edit"]) && empty($_POST["delete"])) {
            $selectQuery = "select name, comment from bbs where id=".mysqli_real_escape_string($link, $_POST["edit"]);
            $res = mysqli_query($link, $selectQuery);
            $data = mysqli_fetch_assoc($res);
            $presentName = $data["name"];
            $presentComment = $data["comment"];
        }

        // 削除番号が送信された時に，番号がidの中にあるか確認
        if (!empty($_POST["delete"])) {
            $deleteFlag = checkId($link=$link, $post=$_POST["delete"]);
        } else {
            $deleteFlag = FALSE;
        }
        // 編集番号が送信された時に，番号がidの中にあるか確認
        if (!empty($_POST["edit"])) {
            $editFlag = checkId($link=$link, $post=$_POST["edit"]);
        } else {
            $editFlag = FALSE;
        }
    ?>

    <form action="" method="post">
        名前：<input type="text" name="name", value="<?php if(!empty($presentName)){echo $presentName;}?>"> <br>
        コメント：<input type="text" name="comment", value="<?php if(!empty($presentName)){echo $presentComment;}?>"> <br>
        パスワード：<input type="text" name="password"> <br>
        削除番号：<input type="text" name="delete"> <input type="hidden" name="deleteId" value="<?php if($deleteFlag){echo $_POST["delete"];}?>"> <br>
        編集番号：<input type="text", name="edit"> <input type="hidden" name="editId", value="<?php if($editFlag){echo $_POST["edit"];} ?>"> <br>
        <?php
            if (!empty($_POST["delete"])) {
                if ($deleteFlag) {
                    echo $_POST["delete"]."番を削除するには以下にパスワードを入力して削除ボタンを押してください．<br> <input type='text' name='conformation_password'><br>";
                    echo "<button type='submit'>削除</button> <button type='button' onclick='history.back()'>キャンセル</button> <br>";
                } else {
                    echo "入力された番号は無効です．<br>";
                    echo "<button type='submit'>送信</button> <br>";
                    $_POST["delete"] = NULL;
                }
            } else if (!empty($_POST["edit"])) {
                if ($editFlag) {
                    echo $_POST["edit"]."番を編集するにはパスワードを入力して編集ボタンを押してください．<br> <input type='text' name='conformation_password'> <br>";
                    echo "<button type='submit'>編集</button> <button type='button' onclick='history.back()'>キャンセル</button> <br>";
                } else {
                    echo "入力された番号は無効です．<br>";
                    echo "<button type='submit'>送信</button> <br>";
                }
            } else {
                echo "<button type='submit'>送信</button> <br>";
            }

            if (!empty($_POST["editId"])) {
                // 編集モードで入力フォームが送られたときの動作
                $passwordQuery = "select password from bbs where id=".mysqli_real_escape_string($link, $_POST["editId"]);
                $res = mysqli_query($link, $passwordQuery);
                $password = mysqli_fetch_assoc($res)['password'];

                if ($_POST["conformation_password"] == $password) {
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $updateQuery = "update bbs set name='".mysqli_real_escape_string($link, $name)."',comment='"
                    .mysqli_real_escape_string($link, $comment)
                    ."' where id=".mysqli_real_escape_string($link, $_POST["editId"]);
                    mysqli_query($link, $updateQuery);
                    echo $_POST["editId"]."番の投稿を編集しました．<br>";
                } else {
                    echo "パスワードが間違っています．再度編集番号から入力しなおしてください．<br>";
                }
            } elseif (!empty($_POST["deleteId"])) {
                // 削除モードでの動作
                $passwordQuery = "select password from bbs where id=".mysqli_real_escape_string($link, $_POST["deleteId"]);
                $res = mysqli_query($link, $passwordQuery);
                $password = mysqli_fetch_assoc($res)['password'];

                if ($_POST["conformation_password"] == $password) {
                    $deleteQuery = "delete from bbs where id=".$_POST["deleteId"];
                    mysqli_query($link,  $deleteQuery);
                    echo $_POST["deleteId"]."番の投稿を削除しました．<br>";
                } else {
                    echo "パスワードが間違っています．再度削除番号から入力しなおしてください．<br>";
                }
            } elseif (array_filter($_POST) && empty($_POST["edit"]) && empty($_POST["delete"])) {
                if (!empty($_POST["name"])) {
                    $name = $_POST["name"];
                } else {
                    $name = "名無し";
                }
                if (!empty($_POST["comment"])) {
                    $comment = $_POST["comment"];
                } else {
                    $comment = "コメントなし";
                }
                $password = $_POST["password"];
                // 番号管理のためのファイルからと現在の投稿番号を読み込む．
                $insertPost = "insert into bbs(
                    name,
                    comment,
                    password
                ) values ('"
                    .mysqli_real_escape_string($link, $name)."','"
                    .mysqli_real_escape_string($link, $comment)."','"
                    .mysqli_real_escape_string($link, $password)
                ."')";
                mysqli_query($link, $insertPost);
            } elseif (!array_filter($_POST) && !empty($_POST)) {
                // フォームに何も入力されていない時の表示
                echo "フォームに何も入力されていません．<br>";
            }

            // 名前の一覧をフォームの下に表示する．
            $selectQuery = "select id, name, comment, time_posted from bbs";
            $res = mysqli_query($link, $selectQuery);
            $data = array();
            while($row = mysqli_fetch_assoc($res)){
                array_push($data, $row);
            }
            arsort($data);
            foreach( $data as $key => $val ){
                echo "----------------------------------------------------------------------------------------------------<br>";
                echo "投稿番号：".$val['id']."<br>名前：".$val['name']."<br>コメント：".$val['comment']."<br>投稿日時：".$val['time_posted'].'<br>';
            }

            mysqli_close($link);
        ?>
    </form>
</body>
</html>

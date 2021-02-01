<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kadai_4</title>
</head>
<body>
    <form action="" method="post">
        テキストを入力してください．
        <input type="text" name="text" id="">
        <input type="submit" value="submit">
    <?php
        if (!empty($_POST["text"])) {
            echo $_POST['text'];
            }
    ?>
    </form>
</body>
</html>
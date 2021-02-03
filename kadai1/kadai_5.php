<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kadai_5</title>
</head>
<body>
    <form action="" method="post">
        テキストを入力してください．
        <input type="text" name="text" id="">
        <input type="submit" value="submit">
    <?php
        if (!empty($_POST["text"])) {
            $filePath = './kadai_5.txt';
            touch($filePath);
            chmod($filePath, 0666);

            file_put_contents($filePath, $_POST['text']);
            echo $filePath.'に入力したテキストを書き込みました．';
            }
    ?>
    </form>
</body>
</html>
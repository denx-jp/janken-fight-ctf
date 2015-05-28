<?php
$db = new PDO('sqlite:' . __DIR__ . '/ranking.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if(!empty($_POST['username'])) {
    $username = mb_substr($db->quote($_POST['username']), 0, 50);
    mb_regex_encoding("UTF-8");
    if (!preg_match("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u", $username)) {
        return;
    }
    $count = (int) $_POST['count'];
    $query = sprintf("insert into ranking values(%s, %d)", $username, $count);
    try {
        $db->exec($query);
    } catch(Exception $e) {
        var_dump($e);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ジャンケンファイト on WEB!</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <style>
#battle, #register {
display: none;
}
    </style>
</head>
<body>

<div class="container">

    <h1>ジャンケンファイト！ on WEB</h1>
    <p><img src="hikakin.jpg" alt="ブンブンジャンケン"></p>
    <p>君は何連勝できるかな！？ランキングで競い合おう！</p>
    <form id="battle">
    <h2 id="message"></h2>
    <p><span id="username"></span>さんの連勝記録： <span id="count"></span> 回</p>
    <input class="btn btn-primary" type="submit" value="グー" />
    <input class="btn btn-success" type="submit" value="チョキ" />
    <input class="btn btn-danger" type="submit" value="パー" />
    </form>

    <form id="start"> 
        <h2>あなたの名前：<input type="text" id="input-username"></h2>
        <input class="btn btn-primary" type="submit" name="start" value="スタート！">
    </form>

    <h2>連勝者ランキング</h2>
    <form id="register" action="janken.php" method="POST">
    <input class="btn btn-primary" name="register" type="submit" value="ランキング登録！">
    </form>
    <table class="table table-striped">
    <tr>
        <th>順位</th>
        <th>ユーザー名</th>
        <th>連勝数</th>
    </tr>
    <?php foreach($db->query('select * from ranking order by `count` desc') as $i => $row) : ?>
    <tr>
        <td><?php echo ($i + 1) ?></td>
        <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?php echo number_format($row['count']) ?></td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
    
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script>
$(function() {
    $('#start').submit(function() {
        var username = $('#input-username').val();
        $('#username').text(username);
        $('#count').text('0');
        $('#start').hide();
        $('#battle').show();
        $('#register').show();
        return false;
    });

    $('#battle').submit(function() {
        var rand = Math.floor(Math.random() * 3);
        var message = '';
        var count = parseInt($('#count').text());
        if(rand == 0) {
            message = 'あなたの勝ち！';
            count += 1;
        } else if (rand == 1) {
            message = 'あなたの負け...';
            count = 0;
        } else {
            message = 'あいこです';
            count = 0;
        }
        $('#message').text(message);
        $('#count').text(count.toString());
        return false;
    });

    $('#register').submit(function() {
        $.post('janken.php', {username: $('#username').text(), count: $('#count').text()})
        .done(function() {
            location.reload();
        });
        return false;
    });
});
</script>
</body>
</html>

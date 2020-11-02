<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title> 
</head>

<body>
    
<?php

// DB接続設定
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成(テーブル名；tbm5)
$sql = "CREATE TABLE IF NOT EXISTS tbm5"
." ("
. "Id INT AUTO_INCREMENT PRIMARY KEY,"
. "Name char(32),"
. "Comment TEXT,"
. "Date DATETIME,"
. "Password char(16)"
.");";
$stmt = $pdo->query($sql);

//エラー非表示
error_reporting(E_ALL ^ E_NOTICE);

//POST
//日付用
$date = new DateTime();
$date = $date->format('Y-m-d H:i:s');
//新規投稿用
$name = $_POST["name"];
$text = $_POST["text"];
$pass = $_POST["pass"];
//削除用
$delnum = $_POST["delnum"];
$delpass = $_POST["delpass"];
//編集準備用
$editnum = $_POST["editnum"];
$editpass = $_POST["editpass"];
//編集用
$editname = $_POST["editname"];
$edittext = $_POST["edittext"];
$editnumber = $_POST["editnumber"];
$editpassword = $_POST["editpassword"];

//新規投稿
if(!empty($name && $text && $pass) && empty($delnum or $delpass) && empty($editnum or $editpass)){
    $sql = $pdo -> prepare("INSERT INTO tbm5 (Name, Comment, Date, Password) VALUES (:Name, :Comment, :Date, :Password)");
    $sql -> bindParam(':Name', $Name, PDO::PARAM_STR);
    $sql -> bindParam(':Comment', $Comment, PDO::PARAM_STR);
    $sql -> bindParam(':Password', $Password, PDO::PARAM_STR);
    $sql -> bindParam(':Date', $Date, PDO::PARAM_STR);
    $Name = $name;
    $Comment = $text;
    $Password = $pass;
    $Date = $date; 
    $sql -> execute();
}

//削除
if(!empty($delnum && $delpass) && empty($name or $text or $pass) && empty($editnum or $editpass)){
    $id = $delnum;
	$sql = 'delete from tbm5 where id=:Id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':Id', $id, PDO::PARAM_INT);
	$stmt->execute();
}

//編集準備
if(!empty($editnum && $editpass) && empty($name or $text or $pass) && empty($delnum or $delpass)){
    $id = $editnum;
    $sql = 'SELECT * FROM tbm5 WHERE id=:Id ';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':Id', $id, PDO::PARAM_INT);
    $stmt->execute();                             
    $results = $stmt->fetchAll(); 
	foreach ($results as $row){
		$peditname = $row['Name'];
		$pedittext = $row['Comment'];
	}
}
//編集
if(!empty($editname && $edittext)){
	$sql = 'UPDATE tbm5 SET Name=:Name,Comment=:Comment,Password=:Password,Date=:Date WHERE Id=:Id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':Id', $Id, PDO::PARAM_INT);
	$stmt->bindParam(':Name', $Name, PDO::PARAM_STR);
	$stmt->bindParam(':Comment', $Comment, PDO::PARAM_STR);
	$stmt->bindParam(':Date', $Date, PDO::PARAM_STR);
	$stmt->bindParam(':Password', $Password, PDO::PARAM_STR);
	$Id = $editnumber;
	$Name = $editname;
    $Comment = $edittext;
    $Date = $date;
    $Password = $editpassword;
	$stmt->execute();
}
    
//データ表示
$sql = 'SELECT * FROM tbm5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
	echo $row['Id'].'<>';
	echo $row['Name'].'<>';
	echo $row['Comment'].'<>';
	echo $row['Date'].'<br>';
echo "<hr>";
}

?>

<hr>
<form action="" method="post">
    
    <?php
    if(empty($peditname && $pedittext)){ 
    ?>
    <input type="name" name="name" placeholder="名前">
    <input type="text" name="text" placeholder="コメント">
    <input type="text" name="pass" placeholder="パスワード">
    <input type="submit" name="submit" value="投稿"><br>
        
    <input type="number" name="delnum" placeholder="削除番号指定">
    <input type="text" name="delpass" placeholder="パスワード">
    <input type="submit" name="delete" value="削除"><br>
        
    <input type="number" name="editnum" placeholder="編集番号指定">
    <input type="text" name="editpass" placeholder="パスワード">
    <input type="submit" name="edit" value="編集">
    <?php
    echo "<br>※投稿、削除、編集はそれぞれ同時にはできません";
    ?>
        
    <?php 
    }elseif(!empty($editnum && $peditname && $pedittext)){
        echo "<br>". $editnum. "番の投稿を編集します。<br>"
        ."名前かコメントを編集してください。<br>";
    ?>
    <input type="name" name="editname" value="<?php echo $peditname; ?>" placeholder="名前">
    <input type="text" name="edittext" value="<?php echo $pedittext; ?>"placeholder="コメント">
    <input type="submit" name="edit" value="編集"><br>
    <input type="hidden" name="editnumber" value="<?php echo $editnum; ?>">
    <input type="hidden" name="editpassword" value="<?php echo $editpass; ?>"><br>
    <?php 
    }
    ?>
        
</form>
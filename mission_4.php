<?php

//===データベースへの接続===================================================================//
  try{                                             // try内に接続する情報を記述
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn,$user,$password);
  } catch (PDOException $e) {                      // catchでエラー情報を投げる
    exit('データベース接続失敗'.$e->getMessage());
  }

//===テーブルの作成========================================================================//
  $sql = "CREATE TABLE tb_mission4"
  ."("
  ."'id' INT,"
  ."'name' char(32),"
  ."'comment' TEXT,"
  ."'ymdt' char(32),"
  ."'pass' char(32)"
  .");";
  $stmt = $pdo -> query($sql);

/*
//===テーブル一覧を表示==========================================================================================//
  $sql = 'SHOW TABLES';
  $result = $pdo -> query($sql);
  foreach ($result as $row){
//    echo $row[0]; //row[0]にはtbtestが格納されており、これがechoで表示された
//    echo '<br>';
print_r($row);
  }
  echo "<hr>"; // <hr>:水平線を入れるタグ
*/

/*
//===テーブルの中身を確認============================================//
  $sql = 'SHOW CREATE TABLE tb_mission4';
  $result = $pdo -> query($sql);
  foreach ($result as $row){
    print_r($row); //print_r():配列を指定するとキーと値を表す形式で出力
  }
  echo "<hr>";
//echo $row[0]."<br>"; //test
//  row[0]: tbtest
//  row[1]: CREATE TABLE `tbtest` ( `id` int(11) DEFAULT NULL, `name` char(32) DEFAULT NULL, `comment` text ) ENGINE=MyISAM DEFAULT CHARSET=utf8 
*/


//===編集対象番号の指定と表示===============================================================================//
  if(!empty($_POST['edinum']) and isset($_POST['edit'])){ //loop P if 編集番号指定されてボタンが押された場合
    $ediline = -1;
    $edipassTorF = 0; //パスワード正誤(1:True,0:False)
    $edilineTorF = 0; //編集番号有無(1:True,0:False)
    $_POST['edited'] = $_POST['edinum']; // hiddenに編集対象番号を格納(9/1追加)、ないと追記になってる(9/2に確認)
    $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
    $results = $pdo -> query($sql);
    foreach ($results as $row){
//      if ($row['id']==$_POST['edinum'] and $row['pass']==$_POST['edipasinput']){ //編集番号とパスワードが一致したとき(180922:if文を分割)
      if ($row['id']==$_POST['edinum']){ //編集番号が一致したとき
         $ediline = $row['id']; //書き込みファイル中での編集行数の要素番号を取得
         $edilineTorF = 1; //編集番号が合うとき(エラー表示に使用)
//echo "edipassinput=".$_POST['edipassinput']."<br>"; //test
//echo "row['pass']=".$row['pass']."<br>"; //test
         if ($row['pass']==$_POST['edipassinput']){ //(編集番号と)パスワードが一致したとき
//           echo "投稿番号".$_POST['edited']."の内容を編集します。<br>";
           $editednum = $row['id']; //編集番号を記憶
           $ediname = $row['name']; //編集行数での投稿者名を取得
           $edicomment = $row['comment']; //編集行数でのコメントを取得
           $edipassTorF = 1;
           $_POST['editmode'] = $row['id']; //editmodeの中身はここで取得、投稿フォームのhiddenはここで埋める
         }else{ //編集番号はあるが、パスワードが異なるとき
//           echo "パスワードが違います。<br>";
         }
      }else{
//         echo "編集番号が違います。確認してください。<br>";
      }
//echo "ediline=".$ediline." editednum=".$editednum." edipassTorF=".$edipassTorF."<br>"; //test:editednumには格納されず
    }
  } //loop P

?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      input[type="text"] { width: 13em; };
    </style>
  </head>
  <body>
<?php echo "デフォルトのパスワードはpasswordです(適切に書き換えて投稿してください)。<br>一度登録したパスワードの変更は現在不可能です。<br>削除・編集番号には0を入れないでください。<br>"; ?>
    <p><送信フォーム></p>
    <form method="POST" action="mission_4.php">                      <!-投稿&編集用フォーム->
<?php /*echo "1:edited=".$_POST['edited']."edipassinput=".$_POST['edipassinput']." edipassTorF=".$edipassTorF."<br>"; */?>
      <?php if($_POST['edited'] == "") : ?>                              <!-新規投稿の場合：hiddenからeditedに変更->
<?php /*echo "2:edited=".$_POST['edited']."edipassinput=".$_POST['edipassinput']." edipassTorF=".$edipassTorF."<br>"; */?>
        <input type="text" name="name_html" placeholder="名前" value="<?php echo $ediname; ?>" /><br />
        <input type="text" name="comment_html" placeholder="コメント" value="<?php echo $edicomment; ?>" /><br />
        <input type="text" name="password_html" value="password" /> 
        <input type="submit" name="submit" value="送信" /><br>
      <?/*php elseif($_POST['edipassinput'] == $passdata2[0] and $ediline != -1) : */?>     <!-編集の場合(hiddenに格納あり)でパスワード一致or編集番号なし->
      <?php elseif($_POST['edinum'] != 0 and $edipassTorF==1 and $ediline != -1) : ?>     <!-編集の場合(hiddenに格納あり)でパスワード一致or編集番号なし->
<?php /*echo "3:edited=".$_POST['edited']."edipassinput=".$_POST['edipassinput']." edipassTorF=".$edipassTorF."<br>"; */?>
        <input type="text" name="name_html" value="<?php echo $ediname; ?>" /><br />
        <input type="text" name="comment_html" value="<?php echo $edicomment; ?>" /><br />
        <input type="submit" name="submit" value="送信" />
        <input type="hidden" name="editmode" value="<?php echo $editednum; ?>" /><br>
      <?php else : ?>                                                    <!-編集の場合(hiddenに格納あり)でパスワード不一致->
<?php /*echo "4:edited=".$_POST['edited']."edipassinput=".$_POST['edipassinput']." edipassTorF=".$edipassTorF."<br>"; */?>
        <input type="text" name="name_html" value="編集番号かパスワードが違います" disabled /><br />
        <input type="text" name="comment_html" value="編集番号かパスワードが違います" disabled /><br />
        <input type="submit" name="submit_init" value="はじめのページへ" /> <!-180922shumit_initに変更->
        <input type="hidden" name="editmode" value="<?php echo $editednum; ?>" disabled /><br>
      <?php endif; ?>
    </form>
    <p><削除・編集フォーム></p>
    <form method="POST" action="mission_4.php">                      <!-削除用フォーム->
      <input type="text" name="delnum" placeholder="削除対象番号" /><br>
      <input type="text" name="delpassinput" placeholder="パスワード入力欄" />
      <input type="submit" name="delete" value="削除" /><br>
    </form>
    <form method="POST" action="mission_4.php">                      <!-編集番号指定フォーム->
      <input type="text" name="edinum" placeholder="編集対象番号" /><br>
      <input type="text" name="edipassinput" placeholder="パスワード入力欄" />
      <input type="submit" name="edit" value="編集" /> 
      <input type="hidden" name="edited" value ="<?php echo $editednum; ?>" /><br><br>
    </form>
  </body>
</html>

<?php
//===新規投稿と編集内容の投稿=====================================================================//
  if(!empty($_POST['comment_html']) and isset($_POST['submit']) and !empty($_POST['name_html'])){ // loop Z
    if($_POST['editmode']==""){ //新規投稿モード
//      $sql_id = 'SELECT COUNT(id) FROM tb_mission4';
      $sql_id = 'SELECT MAX(id) FROM tb_mission4';
      $id_before = $pdo -> query($sql_id);
//echo $id_before -> fetchColumn()."<br>"; //投稿前の番号が正しく表示。ここで実行すると次の実行ではその次の行を見ることになって2回目の実行結果が空になる
      $num_id_before = $id_before->fetchColumn(); //149行目の処理の結果を変数に格納しているつもり。先に(1回目の実行で)結果を代入する
//echo $num_id_before."<br>"; // 出力は空(改行のみされている)。先に代入しておくと正しく表示}

      $sql = $pdo -> prepare("INSERT INTO tb_mission4 (id, name, comment, ymdt, pass) VALUES (:id, :name, :comment, :ymdt, :pass)");
      $sql -> bindParam(':id', $id, PDO::PARAM_INT);
      $sql -> bindParam(':name', $name, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql -> bindParam(':ymdt', $ymdt, PDO::PARAM_STR);
      $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

      $id = $num_id_before + 1;
      $name = $_POST['name_html'];
      $comment = $_POST['comment_html'];
      $ymdt = date("Y/m/d H:i:s");
      $pass = $_POST['password_html'];//."<>";
      $sql -> execute();

    //---投稿内容の表示---------------------//
      echo $id."番の投稿を受け付けました<br>";
      $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
      $results = $pdo -> query($sql);
      foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['ymdt'].'<br>';
      }
    //--------------------------------------//
    }else { //編集モード
//echo "edinum=".$_POST['edinum']." editmode=".$_POST['editmode']."<br>"; //test:edinumは空、editmodeは格納
        $id_edit = $_POST['editmode'];
        $nm = $_POST['name_html'];
        $kome = $_POST['comment_html'];
        $ymdt2 = date("Y/m/d H:i:s");
        $sql = "update tb_mission4 set name='$nm', comment='$kome', ymdt='$ymdt2' where id='$id_edit'";// AND pass='$pass'";
        $result = $pdo->query($sql);
//echo "id_edit=".$id_edit." nm=".$nm." kome=".$kome."<br>"; //test
        //---編集後の表示---------------------//
        echo "投稿番号".$id_edit."の内容を編集しました。<br>";
        $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
        $results = $pdo -> query($sql);
        foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
          echo $row['id'].',';
          echo $row['name'].',';
          echo $row['comment'].',';
          echo $row['ymdt'].'<br>';
        }
        //------------------------------------//
    }
  } //loop Z end
//============================================================================================//

//===編集対象の表示===========================================================================//
  if(!empty($_POST['edinum']) and isset($_POST['edit'])){ //loop P if 編集番号指定されてボタンが押された場合
    //---編集対象の表示---//
    if($_POST['edinum'] != 0 and $edilineTorF==1){ // !=0の効果はなし
      if($edipassTorF==1){
        echo $_POST['edited']."番の投稿を編集します。送信フォームから編集した投稿を送信してください。<br>";
      }else{
        echo "パスワードが違います。<br>";
      }
    }else{
      echo "編集番号が違います。確認してください。<br>";
    }
    $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
    $results = $pdo -> query($sql);
    foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['ymdt'].'<br>';
    }
  }
//====================================================================================//


//===入力データの削除(delete)=========================================================//
  if(!empty($_POST['delnum']) and isset($_POST['delete'])){
    //---入力された削除番号、パスワードを変数に格納---//
    $id = $_POST['delnum'];
    $pass = $_POST['delpassinput'];//."<>";
//echo "id= ".$id." pass= ".$pass."<br>"; //test
    //---表示のための操作(削除実行に際しては必要なし)---//
    $delline = -1; //削除対象番号判定(-1以外：あり、-1:なし)
    $delpassTorF = 0; //削除パスワード正誤(T:1,F:0)
    $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
    $results = $pdo -> query($sql);
    foreach($results as $row){
      if($row['id']==$id){
        $delline = $id;
        if($row['pass']==$pass){
          $delpassTorF = 1;
        }
      }
    }

    //---削除の実行---//
    $sql = "delete from tb_mission4 where id='$id' AND pass='$pass'"; // $idと$passを''で囲まないと処理されなかった
    $result = $pdo->query($sql);
    //---削除後の表示---//
//echo "削除後画面<br>";
    if($delline != -1){
      if($delpassTorF==1){
        echo $id."番の投稿を削除しました。<br>";
      }else{
        echo "パスワードが違います。<br>";
      }
    }else{
      echo "削除番号が違います。確認してください。<br>";
    }
    if($_POST['delnum'] == 0){
      echo "削除番号が違います。確認してください。<br>";
    }

    $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
    $results = $pdo -> query($sql);
    foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['ymdt']."<br>";
//      echo $row['pass']."<br>";
    }
  }
//========================================================================================//

//===処理なしの場合の表示(ボタン押されていないor編集不可後のボタン押した後)==============//
  if ( !isset($_POST['submit']) and !isset($_POST['delete']) and !isset($_POST['edit'])){
echo "ホーム画面<br>";
    $sql = 'SELECT * FROM tb_mission4 ORDER BY id';
    $results = $pdo -> query($sql);
    foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
      echo $row['id'].',';
      echo $row['name'].',';
      echo $row['comment'].',';
      echo $row['ymdt'].'<br>';
    }
  }
//========================================================================================//

?>
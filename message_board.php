<?php 
  session_start();
  require_once('./conn.php');
  include_once('add_on.php');
?>
<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="content-type" content="text">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Message_Borad</title>
  <link href="./css/style.css" rel="stylesheet"/>
  <script
  src="https://code.jquery.com/jquery-3.4.1.js"   
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="   
  crossorigin="anonymous">
  </script>
</head>
  <body>
    <div class="container">
    <!--導覽列-->
      <nav>
        <div class="page__title">MESSAGE BOARD</div>
        <div class="alert__info">
          <span>本站為練習用網站，因教學用途刻意忽略資安實作，註冊時請勿使用任何真實帳號或密碼</span>
        </div>
        <div class="account__info">
          <?php
            if(isset($_SESSION["SID"])) {
              echo '<span class= "logined__username nav__btn">'
            . escape($_SESSION['NICKNAME']) .'</span>';
            } else {
              echo 'login failed';
              header('Location: ./login.php');
            }
          ?>
          <a href="./handle_logout.php" class="nav_btn">登出</a>
        </div>
      </nav>
    <!--留言板-->
      <div class="message__board">
        <div class="board__title">留言帖
          <span class="message__number">
          <?php 
            $msg_num = getPageResult($conn);
            echo $msg_num;
          ?>
          </span>
        </div>
        <form class = "reply__section">
          <textarea class="reply__input" name="content" rows="10"></textarea>
          <input type='hidden' name="user_id"
                 value="<?php echo escape($_SESSION['UID']);?>"/>
          <div class="btn submit__msg"/>送出</div>
        </form>
    <!--現有留言-->
        <div class="message__section">
        <?php 
          $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
          $limit = 10;
          $page_start = (($page-1) * $limit) + 1;
          $page_num = ceil($msg_num / $limit);
          //取得留言
          printMessage($conn, $page, $_SESSION['UID']);
          //生成頁碼(內部echo)
          printPagination($page, $page_num);
          //資料總數
          printDataInfo($page_start, $msg_num);
        ?>
        <div class="test"></div>
    </div>
    <script src='./ajax.js' type='text/javascript'></script>
</body>
</html>
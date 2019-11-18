<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="content-type" content="text/html">
  <title>W9H2__loginpage__yoding</title>
  <link href="./css/account_page.css" rel="stylesheet"/>
</head>
<body>
  <div class="login__page">
    <div class="login__box">
      <div class="box__option">
        <div class="login opt">登入</div>
        <div class="signup opt"><a href="./signup.php">註冊</a></div>
      </div>
      <div class="login__input">
        <form method="POST" action="./handle_login.php">
          <input type="text" placeholder="使用者名稱" name='login__username'>
          <input type="text" placeholder="密碼" name='login__password'>
          <input type='submit' value="送出" class="submit__btn"/>
        <form/>
      </div>
    </div>
  </div>
</body>
</html>
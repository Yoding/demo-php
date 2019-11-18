<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="content-type" content="text/html">
  <title>W9H2__loginpage__yoding</title>
  <link href="./css/account_page.css" rel="stylesheet"/>
</head>
<body>
  <div class="signup__page">
    <div class="signup__box">
      <div class="box__option">
        <div class="signup opt"><a href="./login.php">登入</a></div>
        <div class="sign__up opt">註冊</div>
      </div>
      <div class="signup__input">
        <form method="POST" action="./handle_add_user.php">
          <input type="text" placeholder="使用者名稱" name='username'>
          <input type="text" placeholder="暱稱" name='nickname'>
          <input type="text" placeholder="密碼" name='password'>
          <input type="text" placeholder="確認密碼" name='password2'>
          <input type='submit' value="送出" class="submit__btn"/>
        <form/>
      </div>
    </div>
  </div>
</body>
</html>
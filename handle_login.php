<?php
  session_start();

  function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
  }
  
  require_once('./conn.php');
  $login_username = $_POST['login__username'];
  $login_password = $_POST['login__password'];
  if (!empty($login_username) && !empty($login_password)) {
    $stmt = $conn->prepare("SELECT * FROM yoding_users WHERE username = ?");
    $stmt->bind_param("s", $login_username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result -> num_rows > 0) {
      $row = $result->fetch_assoc();
      // hash-verify
        if (password_verify($login_password, $row['password'])) {
            $session_id = time() . bin2hex(random_bytes(8));
            // storing session value
            $_SESSION["SID"] = $session_id;
            $_SESSION["UID"] = $row['user_id'];
            $_SESSION["NICKNAME"] = $row['nickname'];
              // check and redirection
              if (isset($_SESSION["SID"])) {
                header('Location: ./message_board.php?page=1');
              } 
        } else { echo 'Invalid password.';} 
    } else { echo "Failed" . $conn->error;}
  } else { die('請確認各欄位均已填入');}
?>

<?PHP
  require_once('./conn.php');
  session_start();
  if(isset($_SESSION['SID'])) {
    unset($_SESSION['SID']);
    unset($_SESSION['UID']);
    unset($_SESSION['NICKNAME']);
    header('Location: ./login.php');
  } else {
    die('Failed to log out. Please try again.');
  }
  session_destory();
?>
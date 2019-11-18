
<?php
require_once('./conn.php');
// $method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'];
//set response message
$response_scuess = array(
  'result' => 'scuessed',
  'message' => '操作成功'
);
$response_fail = array(
  'result' => 'failed',
  'message' => '操作失敗，請再試一次'
);

switch ($action) {
  case "add_msg";
    $user_id = $_POST['user_id'];
    $parent_msg_id = $_POST['parent_msg_id'];
    $content = $_POST['content'];
    // insert data into DB
    $sql_add= "INSERT INTO yoding_comments (user_id, parent_msg_id, content) 
               VALUES(?, ?, ?)";
    $stmt = $conn->prepare($sql_add);
    $stmt->bind_param("iis", $user_id, $parent_msg_id, $content);
    // get consequence reply
    if($stmt->execute()) {
      $id = $stmt->insert_id;
      $stmt = $conn->prepare("SELECT * FROM yoding_comments WHERE message_id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result ->fetch_assoc();
      // set response
      $array = array(
        'id' => $id,
        'content' => $row['content'],
        'time' => $row['created_at'],
      );
      echo json_encode($array);
    } else {
      echo json_encode($response_fail);
    }
  break;

  case "del_msg";
    $id = $_POST['id'];
    $sql_del = "DELETE FROM yoding_comments where message_id = ?";
    $stmt = $conn->prepare($sql_del);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      echo json_encode($response_scuess);
    } else {
      echo json_encode($response_fail);
    }
  break;

  case "edit_msg";
    $id = $_POST['id'];
    $content = $_POST['content'];
    $sql_edit= "UPDATE yoding_comments SET content = ? WHERE message_id = ?";
    $stmt = $conn->prepare($sql_edit);
    $stmt->bind_param("si", $content, $id);
    if ($stmt->execute()) {
      echo json_encode($response_scuess);
    } else {
      echo json_encode($response_fail);
    }
  break;
}
?>
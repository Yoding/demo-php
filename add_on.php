
<?php
require_once('./conn.php');
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
  }

function runPreQuery($conn, $sql, $id) {
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    $result = $stmt->get_result();
  } else { return "Query failed.";}
  return $result;
}

function renderEditBlock($content){
  $edit_block = "<div class='btn edit__msg'>編輯</div>
                <div class='btn del__msg'>刪除</div>
                <form class='edit__msg__form'>
                  <input class='edit__text' value='" .escape($content) . "'/>
                  <div class='btn submit__edited__msg'>Submit</div>
                 </form>";
  return $edit_block;
}

function renderSubMsg($subrow, $msg_userid, $login_id) {
  // sub-message components
  $del_submsg = "<span class='del__submsg' submsg-id='" 
                  . escape($subrow['message_id']) . "'> x </span>";
  $origin = (($subrow['user_id'] === $msg_userid)? "same":"other");
  $del_premission = (($subrow['user_id'] === $login_id)? $del_submsg:"");
  // sub-message block
  $submsg_block = "<div class='current__submsg " . $origin . "'> " 
                     .$del_premission ."
                    <span class='sub__user'>" 
                      . escape($subrow['nickname']) . " : </span>
                    <span class='sub__content'>" 
                      . escape($subrow['content']) . "</span></div>";
  return $submsg_block;
}

function getSubMsg($conn, $sql_sub_msg, $parent_msg_id, $msg_userid, $login_id){
  $result_submsg = runPreQuery($conn, $sql_sub_msg, $parent_msg_id);
  $submsg = '';
  if ($result_submsg->num_rows > 0) {
    while($subrow = $result_submsg->fetch_assoc()) {
      $submsg .= renderSubMsg($subrow, $msg_userid, $login_id);
    }
  } return $submsg;
}

function getMainMsg($conn, $row, $login_id, $sql_sub_msg) {
  //main-message components
  $submsg_data = getSubMsg($conn, $sql_sub_msg, $row['message_id'], $row['user_id'], $login_id);
  $msg_info = "<div class='message' msg-id='" 
                  . escape($row['message_id']) . "'>
                 <div class='info'>
                    <span class='nickname'>" 
                      . escape($row['nickname']) . "</span></br>
                    <span class='time__stamp'>" 
                      . escape($row['created_at']) . "</span>
                 </div>";
  $content =    "<div class='content'>
                  <div class='msg__text'>" 
                    . escape($row['content']) . "</div>
                  <div class='edit__block'>" 
                  . (($row['user_id'] === $login_id) ? 
                    renderEditBlock($row['content']):" ") 
                . "</div></div>";
  $submsg =     "<div class='show__submsg'>-></div>
                  <div class='submsg__block'>
                    <input class='input__submsg'/>
                    <div class='btn submit__submsg'>Submit</div>
                    <div class='submsg'>".$submsg_data. "</div>"
               . "</div>
               </div>";
  $msg_block = ($msg_info . $content . $submsg);
  return $msg_block;
}

function printMessage($conn, $page, $login_id) {
  $limit = 10;
  $limit_term = 'LIMIT ' . (($page-1) * $limit) . ',' . $limit;
  $sql = 'SELECT * FROM yoding_comments as C 
          INNER JOIN yoding_users as U
          ON C.user_id = U.user_id 
          WHERE parent_msg_id = ? 
          ORDER BY created_at DESC ';
  $sql_main_msg = $sql .$limit_term;
  $sql_sub_msg = $sql;
  $result = runPreQuery($conn, $sql_main_msg, '0');
    if ($result->num_rows > 0) {
      while ($row = $result ->fetch_assoc()) {
        echo getMainMsg($conn, $row, $login_id, $sql_sub_msg);
      }
    } else { echo "No results.";}
}

// Page informations
function getPageResult($conn){
  $sql='SELECT * FROM yoding_comments 
        WHERE parent_msg_id = ? ORDER BY created_at DESC';
  $msg_num = (runPreQuery($conn, $sql, '0'))->num_rows;
  return $msg_num;
}

function printPagination($now, $total) {
  echo "<div class='pagination'>";
  // print last page
  echo ($now > 1) ? 
    "<a href='?page=" .($now-1) ."' class='page'>&laquo;</a>" : '';
  // print pages
  for ($i = 1; $i <= $total; $i += 1) {
    echo "<a href='?page=" . $i . "' ";
    echo ($i == $now) ? "class='page active'>" : "class='page'>";
    echo $i . "</a>";
    }
  // print next page
  echo ($now < $total) ? 
    "<a href='?page=" .($now+1) ."' class='page'>&raquo;</a>" : '';
  echo '</div></div>';
}

function printDataInfo($page_start, $total_msg){
  $page_end = $page_start + 9;
  $footer = "<div class = data_footer>
            message" . $page_start 
            . " ~ " .(($page_end < $total_msg) ? $page_end : $total_msg)
            . " of " . $total_msg 
            ."</div>";
  echo $footer;
}
?>

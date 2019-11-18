function htmlEncode(value) {
  return $('.test').text(value).html();
}

  function printMainMsg(msgId, nickname, time, content) {
    const edit = `<div class='btn edit__msg'>編輯</div>
                  <div class='btn del__msg'>刪除</div>
                    <form class='edit__msg__form'>
                      <input class='edit__text' value='${htmlEncode(content)}'/>
                      <div class='btn submit__edited__msg'>Submit</div>
                    <form>`;
    const msg = `<div class='message' msg-id='${msgId}'>
                  <div class='info'>
                    <span class='nickname'>${htmlEncode(nickname)}</span></br>
                    <span class='time__stamp'>${time}</span>
                  </div>
                  <div class='content'>
                    <div class='msg__text'>${htmlEncode(content)}</div>
                    <div class='edit__block'>${edit}</div>
                  </div>
                  <div class='show__submsg'>-></div>
                  <div class='submsg__block'>
                    <input class='input__submsg'/>
                    <div class='btn submit__submsg'>Submit</div>
                    <div class='submsg'></div>
                  </div>
                </div>`;
    return msg;
  }

  function printSubMsg(origin, delPermissioin, msgId, nickname, content) {
    let delOpt = (delPermissioin == 'get'? "<span class='del__submsg' submsg-id='msg_id'> x </span>":"");
    const subMsg =`<div class='current__submsg ${origin}'>
                      ${delOpt}
                     <span class='sub__user'>${htmlEncode(nickname)}: </span>
                     <span class='sub__content'>${htmlEncode(content)}</span>
                   </div>`;
     return subMsg;
   }

  $(document).ready(function() {
  // message and sub-message edit-block toggled 
    $(".edit__msg__form").hide();
    $(".message__section").on("click", ".edit__msg", function() {
    $(this).next().next().toggle();
    });
    $(".submsg__block").hide();
    $(".message__section").on("click", ".show__submsg", function() {
    $(this).next().toggle();
    });
    //common variables set up
    let replyContent = $(".reply__input").val();
    const loginName = $(".logined__username").html();
    const loginUserId = $("input[name=user_id]").val();
    // delete fe message & submsg
    $(".message__section").on("click", ".del__msg", function() {
      const mainMsg = $(this).parents(".message");
      const mainMsgId = $(this).parents(".message").attr("msg-id");
      if(!confirm('等等...確定要刪除這筆留言？')) return;
      $.ajax({
        method: 'POST',
        url: './service.php',
        data: {
          action: 'del_msg',
          id: mainMsgId
        }
      }).done(()=> {
          alert('Change finished.');
          mainMsg.remove();
      }).fail(()=> {
          alert('Failed');
        });
      });

    $(".message__section").on("click", ".del__submsg", function() {
      const subMsgId = $(this).attr('submsg-id');
      const subMsg = $(this).parent();
      $.ajax({
        method: 'POST',
        url: './service.php',
        data: {
          action: 'del_msg',
          id: subMsgId
        }
      }).done(()=> {
          subMsg.remove();
      }).fail(()=> {
          alert('Failed');
        });
      });

    // add main-message
    $(".reply__section").on("click", ".submit__msg", function() {
      let replyContent = $(".reply__input").val();
      $.ajax({
        method: 'POST',
        url: './service.php',
        data: {
          action: 'add_msg',
          user_id: loginUserId,
          parent_msg_id: '0',
          content: replyContent,
        }
      }).done(function(resp) {
        const consq = JSON.parse(resp);
        // ES6 way
        const [id, time, content] = [consq.id, consq.time, consq.content];
        const neoMessage = printMainMsg(id, loginName, time, content);
        $(".reply__input").val('');
        $(".message__section").prepend(neoMessage);
        $(".edit__msg__form").hide();
        $(".submsg__block").hide();
        $(".test").text('');
      }).fail(function(resp) {
        alert('Please try again.');
      });
    });

    // add sub-message
    $(".message__section").on("click", ".submit__submsg", function() {
      const mainMsgId = $(this).parents(".message").attr("msg-id");
      const mainMsgOwner = $(this).parents(".message").find(".nickname").html();
      const subMsg = $(this).next();
      let subMsgContent = $(this).prev();
      let msgOrigin = (loginName === mainMsgOwner)? "same" : "other";
      let delPermissioin = "get";
      $.ajax({
        method: 'POST',
        url: './service.php',
        data: {
          action: 'add_msg',
          user_id: loginUserId,
          parent_msg_id: mainMsgId,
          content: subMsgContent.val(),
        }
      }).done(function(resp) {
        const consq = JSON.parse(resp);
        const [msgId, subcontent] = [consq.msg_id, consq.content];
        const neoSubmessage = printSubMsg(msgOrigin, delPermissioin, msgId, loginName , subcontent);
        subMsg.prepend(neoSubmessage);
        subMsgContent.val('');
        $(".test").text('');
      }).fail(function(resp) {
        alert('Please try again.');
      });
    });

    // edit message
    $(".message__section").on("click", ".submit__edited__msg", function() {
      const mainMsgId = $(this).parents(".message").attr("msg-id");
      const mainMsgContent = $(this).prev().val();
      let originalText  = $(this).parents(".edit__block").prev();
      $.ajax({
        method: 'POST',
        url: './service.php',
        data: {
          action: 'edit_msg',
          content: mainMsgContent, 
          id: mainMsgId
        }
      }).done(() => {
        alert('Changed saved.');
        originalText.text(mainMsgContent);
        $(this).parent().hide();
      }).fail(() => {
        alert('Please try again.');
      });
    });
  });

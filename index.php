<?php
require("db.php");

$result = NULL;
$alert_type = NULL;

function insert_latex($title, $date, $text) {
  try {
    $db = new Database();
  } catch (DatabaseException $e) {
    return array(
      "result" => $e,
      "alert_type" => "alert-error"
    );
  }
  try {
    $id = $db->add_latex($title, $date, $text);
  } catch (DatabaseException $e) {
    return array(
      "result" => $e,
      "alert_type" => "alert-error",
      "id" => $id
    );
  }
  return array(
    "result" => "Your post was successfully submitted.",
    "alert_type" => "alert-success",
    "id" => $id
  );
}

function read_latex($id) {
  $db = new Database();
  try {
    $arr = $db->get_latex($_GET['view_id']);
    return array(
      "result" => $arr,
      "alert_type" => "alert-success"
    );
  } catch (DatabaseException $e) {
    return array(
      "result" => $e,
      "alert_type" => "alert-error"
    );
  }
}

if (isset($_POST['text'])) {
  if (!isset($_POST['title']) || strlen($_POST['title']) == 0) {
    $result = "Please provide a title for your post.";
    $alert_type = "alert-error";
  } else if (strlen($_POST['title']) == 0) {
    $result = "No body text provided.";
    $alert_type = "alert-error";
  } else {
    # Format date like 'August 8th 2005 03:12:46 PM'.
    date_default_timezone_set('America/New_York');
    $arr = insert_latex($_POST['title'], date('F jS Y h:i:s A'), $_POST['text']);
    $result = $arr["result"];
    $alert_type = $arr["alert_type"];
  }
} else if (isset($_GET['view_id'])) {
  if (!is_numeric($_GET['view_id']) || $_GET['view_id'] < 1) {
    $result = "Invalid ID specified.";
    $alert_type = "alert-error";
  } else {
    $arr = read_latex($_GET['view_id']);
    $result = $arr["result"];
    $result["title"] = str_replace("<", "&lt;", $result["title"]);
    $result["title"] = str_replace(">", "&gt;", $result["title"]);
    $result["text"] = str_replace("<", "&lt;", $result["text"]);
    $result["text"] = str_replace(">", "&gt;", $result["text"]);
    $alert_type = $arr["alert_type"];
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>TeXBin</title>
  <meta charset="utf-8" />
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/master.css" rel="stylesheet" />
  <link href="http://fonts.googleapis.com/css?family=Alfa+Slab+One|Cutive" rel="stylesheet" type="text/css" />
  <script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
  </script>
</head>
<body>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="js/bootstrap.min.js"></script>  <script type="text/javascript">
    function preview() {
      var modal_body = document.getElementById("preview-body");
      var latex = $('textarea#text').val();
      latex = latex.replace(">", "&gt;");
      latex = latex.replace("<", "&lt;");
      modal_body.innerHTML = latex;
      MathJax.Hub.Queue(["Typeset", MathJax.Hub, "preview-body"]);
    }
    function selectText(id) {
      document.getElementById(id).focus();
      document.getElementById(id).select();
    }
  </script>
  <script src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML" type="text/javascript">
  MathJax.Hub.Config({
  extensions: ["tex2jax.js"],
  jax: ["input/TeX", "output/HTML-CSS"],
  tex2jax: {
  inlineMath: [ ['$','$'], ["\\(","\\)"] ],
  displayMath: [ ["\\[","\\]"] ],
  },
  "HTML-CSS": { availableFonts: ["TeX"] }
  });
  </script>
  <div id="social">
    <g:plusone annotation="none"></g:plusone>
    <a href="https://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
  </div>
  <div id="github">
    <a href="https://github.com/bcuccioli/TeXBin"><img src="github.png" alt="Fork me on Github" /></a>
  </div>
  <div class="modal hide" id="preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Preview</h3>
    </div>
    <div id="preview-body" class="modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-primary" data-dismiss="modal">Close</button>
    </div>
  </div>
  <div class="hero-unit">
    <h1>TeXBin</h1>
    <?php if ($alert_type == "alert-error"
      || $alert_type == "alert-success" && isset($_POST['text'])) { ?>
    <div class="alert <?=$alert_type?>">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <?=$result?>
    </div>
    <?php } ?>
    <?php if ($alert_type == "alert-success" && isset($_GET['view_id'])) { ?>
    <div class="well well-large">
      <span class="latex-title"><?=$result["title"]?></span><br />
      <span class="latex-date"><?=$result["date"]?></span><br /><br />
      <span class="latex-body"><?=$result["text"]?></span>
    </div>
    <?php } else if ($alert_type == "alert-success" && isset($_POST['text'])) { ?>
    <button type="button" class="btn btn-success">Link</button>
    <input id="link" type="text" value="http://texbin.bcuccioli.com/<?=$arr["id"]?>" />
    <br /><br />
    <script>selectText("link");</script>
    <?php } ?>
    <form action="." method="post" id="posting">
      Title: <input type="text" name="title" /><br />
      <textarea name="text" id="text" style="width: 98%" rows="20"></textarea><br />
      <div id="buttons">
        <a href="#preview" role="button" class="btn" data-toggle="modal" onclick="javascript:preview()">Preview</a>
        <button class="btn btn-primary" name="submit">Post</button>
      </div>
    </form>
  </div>
</body>
</html>

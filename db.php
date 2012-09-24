<?
require("dbexception.php");

class Database {

  public $dbinfo, $mysqli;

  public function __construct() {
    $info = parse_ini_file("../db.ini", true);
    $this->dbinfo = $info["mebrah"];

    $this->mysqli = new mysqli($this->dbinfo["host"],
                                $this->dbinfo["user"],
                                $this->dbinfo["pass"],
                                $this->dbinfo["name"]);

    if ($this->mysqli->connect_errno) {
      throw new DatabaseException($this->mysqli->connect_error);
    }
  }

  public function __destruct() {
    $this->mysqli->close();
    unset($this->dbinfo);
  }

  public function get_latex($id) {
    $title = NULL;
    $date = NULL;
    $text = NULL;
    if (!($stmt = $this->mysqli->prepare("SELECT * FROM texbin WHERE id=?"))) {
      throw new DatabaseException($stmt->error);
    }
    if (!($stmt->bind_param("i", $id))) {
      throw new DatabaseException($stmt->error);
    }
    if (!$stmt->execute()) {
      throw new DatabaseException($stmt->error);
    }
    if (!$stmt->bind_result($id, $title, $date, $text)) {
      throw new DatabaseException($stmt->error);
    }
    $stmt->fetch();

    $stmt->close();
    return array(
      "id" => $id,
      "title" => $title,
      "date" => $date,
      "text" => $text
    );
  }

  public function add_latex($title, $date, $text) {
    $qstr = "INSERT INTO texbin (title, date, text) VALUES (?, ?, ?)";
    if (!($stmt = $this->mysqli->prepare($qstr))) {
      throw new DatabaseException($stmt->error);
    }
    if (!$stmt->bind_param('sss', $title, $date, $text)) {
      throw new DatabaseException($stmt->error);
    }
    if (!$stmt->execute()) {
      throw new DatabaseException($stmt->error);
    }

    $stmt = $this->mysqli->prepare("SELECT id FROM texbin ORDER BY id DESC");
    $stmt->execute();
    $stmt->bind_result($new_id);
    $stmt->fetch();

    $stmt->close();
    return $new_id;
  }
}
?>

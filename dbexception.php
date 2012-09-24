<?
class DatabaseException extends Exception {

  # make including the message compulsory
  public function __construct($dbmsg, $code = 0, Exception $previous = null) {
    $message = "Database error: " . $dbmsg;
    parent::__construct($message, $code);
  }

  # string representation of the exception
  public function __toString() {
    return $this->message;
  }
}
?>

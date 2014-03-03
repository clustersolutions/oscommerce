<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\Language;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\PDO;

  class ErrorHandler {
    static protected $_dbh;

    public static function initialize() {
      ini_set('display_errors', false);
      ini_set('html_errors', false);
      ini_set('ignore_repeated_errors', true);

      if ( is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        ini_set('log_errors', true);
        ini_set('error_log', OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');
      }

      if ( in_array('sqlite', PDO::getAvailableDrivers()) && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Database/') ) {
        set_error_handler(array('osCommerce\\OM\\Core\\ErrorHandler', 'execute'));

        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt') ) {
          static::import(OSCOM::BASE_DIRECTORY . 'Work/Logs/errors.txt');
        }
      }
    }

    public static function execute($errno, $errstr, $errfile, $errline) {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return false;
      }

      switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
          $errors = "Notice";
          break;
        case E_WARNING:
        case E_USER_WARNING:
          $errors = "Warning";
          break;
        case E_ERROR:
        case E_USER_ERROR:
          $errors = "Fatal Error";
          break;
        default:
          $errors = "Unknown";
          break;
      }

      $errstr = Language::toUTF8($errstr);

      $error_msg = sprintf('PHP %s:  %s in %s on line %d', $errors, $errstr, $errfile, $errline);

      $Qinsert = static::$_dbh->prepare('insert into error_log (timestamp, message) values (:timestamp, :message)');
      $Qinsert->bindInt(':timestamp', time());
      $Qinsert->bindValue(':message', $error_msg);
      $Qinsert->execute();

// return true to stop further processing of internal php error handler
      return true;
    }

    public static function connect() {
      $result = false;

      try {
        static::$_dbh = PDO::initialize(OSCOM::BASE_DIRECTORY . 'Work/Database/errors.sqlite3', null, null, null, null, 'SQLite3');
        static::$_dbh->exec('create table if not exists error_log ( timestamp int, message text );');

        $result = true;
      } catch ( \Exception $e ) {
        trigger_error($e->getMessage());
      }

      return $result;
    }

    public static function getAll($limit = null, $pageset = null) {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return array();
      }

      $query = 'select timestamp, message from error_log order by rowid desc';

      if ( is_numeric($limit) ) {
        $query .= ' limit ' . (int)$limit;

        if ( is_numeric($pageset) ) {
          $offset = max(($pageset * $limit) - $limit, 0);

          $query .= ' offset ' . $offset;
        }
      }

      return static::$_dbh->query($query)->fetchAll();
    }

    public static function getTotalEntries() {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return 0;
      }

      $result = self::$_dbh->query('select count(*) as total from error_log')->fetch();

      return $result['total'];
    }

    public static function find($search, $limit = null, $pageset = null) {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return array();
      }

      $query = 'select timestamp, message from error_log where message like :message order by rowid desc';

      if ( is_numeric($limit) ) {
        $query .= ' limit ' . (int)$limit;

        if ( is_numeric($pageset) ) {
          $offset = max(($pageset * $limit) - $limit, 0);

          $query .= ' offset ' . $offset;
        }
      }

      $Qlogs = static::$_dbh->prepare($query);
      $Qlogs->bindValue(':message', '%' . $search . '%');
      $Qlogs->execute();

      return $Qlogs->fetchAll();
    }

    public static function getTotalFindEntries($search) {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return 0;
      }

      $Qlogs = static::$_dbh->prepare('select count(*) as total from error_log where message like :message');
      $Qlogs->bindValue(':message', '%' . $search . '%');
      $Qlogs->execute();

      $result = $Qlogs->fetch();

      return $result['total'];
    }

    public static function import($filename) {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return false;
      }

      $error_log = file($filename);
      unlink($filename);

      $messages = [ ];

      foreach ( $error_log as $error ) {
        $error = Language::toUTF8(trim($error));

        if ( preg_match('/^\[([0-9]{2}-[A-Za-z]{3}-[0-9]{4} [0-9]{2}:[0-5][0-9]:[0-5][0-9].*?)\] (.*)$/', $error, $matches) ) {
          if ( strlen($matches[1]) == 20 ) {
            $timestamp = DateTime::getTimestamp($matches[1], 'd-M-Y H:i:s');
          } else { // with timezone
            $timestamp = DateTime::getTimestamp($matches[1], 'd-M-Y H:i:s e');
          }

          $message = $matches[2];

          $messages[] = [ 'timestamp' => $timestamp,
                          'message' => $message ];
        } elseif ( !empty($messages) ) {
          $messages[(count($messages)-1)]['message'] .= "\n" . $error;
        } else {
          $messages[] = [ 'timestamp' => time(),
                          'message' => $error ];
        }
      }

      foreach ( $messages as $error ) {
        $Qinsert = static::$_dbh->prepare('insert into error_log (timestamp, message) values (:timestamp, :message)');
        $Qinsert->bindInt(':timestamp', $error['timestamp']);
        $Qinsert->bindValue(':message', $error['message']);
        $Qinsert->execute();
      }
    }

    public static function clear() {
      if ( !is_resource(static::$_dbh) && !static::connect() ) {
        return false;
      }

      static::$_dbh->exec('drop table if exists error_log');
    }
  }
?>

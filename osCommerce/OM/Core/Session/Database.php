<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Session;

use osCommerce\OM\Core\OSCOM;

/**
 * The Session\Database class stores the session data in the database
 */

class Database extends \osCommerce\OM\Core\SessionAbstract implements \SessionHandlerInterface
{

/**
 * Initialize database storage handler
 */

    public function __construct()
    {
        session_set_save_handler($this, true);
    }

/**
 * Checks if a session exists
 *
 * @param string $session_id The ID of the session
 */

    public function exists(string $session_id): bool
    {
        return OSCOM::callDB('Session\Database\Check', [
            'id' => $session_id
        ], 'Core');
    }

/**
 * Opens the database storage handler
 */

    public function open($save_path, $name): bool
    {
        return true;
    }

/**
 * Closes the database storage handler
 */

    public function close(): bool
    {
        return true;
    }

/**
 * Read session data from the database storage handler
 *
 * @param string $session_id The ID of the session
 */

    public function read($session_id): string
    {
        $result = OSCOM::callDB('Session\Database\Get', [
            'id' => $session_id
        ], 'Core');

        if ($result !== false) {
            return $result['value'];
        }

        return '';
    }

/**
 * Writes session data to the database storage handler
 *
 * @param string $session_id The ID of the session
 * @param string $session_data The session data to store
 */

    public function write($session_id, $session_data): bool
    {
        return OSCOM::callDB('Session\Database\Save', [
            'id' => $session_id,
            'expiry' => time(),
            'value' => $session_data
        ], 'Core');
    }

/**
 * Deletes the session data from the database storage handler
 *
 * @param string $session_id The ID of the session
 */

    public function destroy($session_id): bool
    {
        return OSCOM::callDB('Session\Database\Delete', [
            'id' => $session_id
        ], 'Core');
    }

/**
 * Garbage collector for the database storage handler
 *
 * @param int $maxlifetime The maxmimum time a session should exist
 */

    public function gc($maxlifetime): bool
    {
        return OSCOM::callDB('Session\Database\DeleteExpired', [
            'expiry' => $maxlifetime
        ], 'Core');
    }
}

<?php

namespace Thor\Http;


/**
 * Common operations on session with an autostart.
 *
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Session
{

    /**
     * Rollbacks changes and terminate the session.
     */
    public static function abort(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_abort();
        }
    }

    /**
     * Rollbacks changes without terminate the session.
     */
    public static function rollback(): void
    {
        self::start();
        session_reset();
    }

    /**
     * Starts a session if not started. If $regenerateId is true, it creates a new session ID.
     */
    public static function start(bool $regenerateId = false): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if ($regenerateId) {
            session_regenerate_id(true);
        }
    }

    /**
     * Deletes all data in session, commit, and delete the session cookie.
     * Then start a session with a new ID.
     */
    public static function delete(): void
    {
        self::clear();
        self::commit();
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            0,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        self::start(true);
    }

    /**
     * Clears the current session.
     * Does not invalidate the cookie, so a start() may retrieve the original data.
     */
    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Writes changes in SESSION and terminate the session.
     */
    public static function commit(): void
    {
        self::start();
        session_write_close();
    }

    /**
     * Removes and unsets one element in session.
     */
    public static function remove(string $name): void
    {
        self::start();
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }

    /**
     * Sets an element in session
     */
    public static function write(string $name, mixed $value): void
    {
        self::start();
        $_SESSION[$name] = $value;
    }

    /**
     * Reads an element from session.
     *
     * Returns $default if the element is not found.
     */
    public static function read(string $name, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$name] ?? $default;
    }

}

<?php

class Session 
{
    private static $timeout = 60 * 45;          // 45 minutes
    private static $logout = 60 * 60 * 1;       // 1h
    private static $logout_admin = 60 * 10;     // 10 minutes


    /**
     * starting the session
     */
    public static function start() {
        if(!session_id()) {
            session_start();
            if(!isset($_SESSION["session_time"]) || isset($_SESSION["session_time"]) && empty("session_time")) {
                self::put("session_time", time());
            }
        }
        if(self::$logout + self::get("session_time") < time() || self::get("user") && self::get("user")["status"] == "admin" && self::$logout_admin + self::get("session_time") < time()) {
            self::delete("user");
        }
        if(self::$timeout + self::get("session_time") < time()) {
            self::regenerate_id();
        }
        return session_id();
    }

    /**
     * to regenerate session id (when login, timeout or other)
     */
    public static function regenerate_id() {
        session_regenerate_id();
        $_COOKIE['PHPSESSID'] = session_id();
        self::put("session_time", time());
    }

    /**
     * to put a pair key => value to the session
     * @param   string      $key
     * @param   string      $value
     * @param   array       $array
     * @return  string
     */
    public static function put($key, $value, $array = null) {
        if($array)
            return $_SESSION[$array][$key] = $value;
        else
            return $_SESSION[$key] = $value;
    }

    /**
     * to have value of a session key
     * @param   string      $key
     * @param   array       $array
     * @return  string|nothing
     */
    public static function get($key, $array = null) {
        if($array && isset($_SESSION[$array][$key]) && !empty($_SESSION[$array][$key]))
            return $_SESSION[$array][$key];
        elseif(isset($_SESSION[$key]) && !empty($_SESSION[$key]))
            return $_SESSION[$key];
        else 
            return null;
    }

    /**
     * to delete value of a session key
     * @param   string      $key
     * @param   array       $array
     * @return  boolean
     */
    public static function delete($key, $array = null) {
        if($array && isset($_SESSION[$array][$key])) {
            unset($_SESSION[$array][$key]);
            return true;
        }
        elseif(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
        else
            return false;
    }

    /**
     * to delete session
     */
    public static function destroy() {
        $_SESSION = array();

        // delete the session cookie
        if(ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - self::$timeout,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }


    /**
     * TOKEN
     */
    /**
     * create CSRF token and generate a new one if expired
     * @return  string
     */
    public static function generate_token() {
        $token = self::get("token");

        if(empty($token) || self::$timeout + self::get("token_time") < time()) {
            self::put("token", md5(bin2hex(openssl_random_pseudo_bytes(8))));
            self::put("token_time", time());
        }
        return self::get("token");
    }

    /**
     * check if CSRF token is still valid
     * @param   string      $token
     * @return  boolean
     */
    public static function check_token_valid($token) {

        if(!empty($token) && $token === self::get("token") && self::$timeout + self::get("token_time") > time())
            return true;
        else
            return false;
    }
}

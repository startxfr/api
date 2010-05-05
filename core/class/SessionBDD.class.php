<?php

/**
 * Description of SessionBDD
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class SessionBDD {

    //private static $_sess_db;

    /**
     * Open the session
     * @return bool
     */
    public static function open() {
        return true;
    }

    /**
     * Close the session
     * @return bool
     */
    public static function close() {
        $bbb = mysql_connect($GLOBALS["DBPool_1"]['serveur'],$GLOBALS["DBPool_1"]['login'],$GLOBALS["DBPool_1"]['pass']);
        mysql_select_db($GLOBALS["DBPool_1"]['base'], $bbb);
        return mysql_close($bbb);
    }

    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public static function read($id) {
        $bbb = mysql_connect($GLOBALS["DBPool_1"]['serveur'],$GLOBALS["DBPool_1"]['login'],$GLOBALS["DBPool_1"]['pass']);
        mysql_select_db($GLOBALS["DBPool_1"]['base'], $bbb);
        $id = mysql_real_escape_string($id);
        $sql = "SELECT backup_sess from session where id_sess = '$id' and datefin_sess > CURRENT_TIMESTAMP";
        if ($result = mysql_query($sql, $bbb)) {
            if (mysql_num_rows($result)) {
                $record = mysql_fetch_assoc($result);
                return $record['backup_sess'];
            }
        }
        return '';
    }

    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public static function write($id, $data) {
        $bbb = mysql_connect($GLOBALS["DBPool_1"]['serveur'],$GLOBALS["DBPool_1"]['login'],$GLOBALS["DBPool_1"]['pass']);
        mysql_select_db($GLOBALS["DBPool_1"]['base'], $bbb);
        $sql = "UPDATE session set backup_sess = '$data' where id_sess = '$id'";
        return mysql_query($sql, $bbb);
    }

    /**
     * Destroy the session
     * @param int session id
     * @return bool
     */
    public static function destroy($id) {
        $bbb = mysql_connect($GLOBALS["DBPool_1"]['serveur'],$GLOBALS["DBPool_1"]['login'],$GLOBALS["DBPool_1"]['pass']);
        mysql_select_db($GLOBALS["DBPool_1"]['base'], $bbb);
        $sql = sprintf("UPDATE `session` set datefin_sess = CURRENT_TIMESTAMP WHERE `id_sess` = '%s'", $id);
        return mysql_query($sql, $bbb);
    }

    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public static function gc($max) {
        return true;
    }
}
?>

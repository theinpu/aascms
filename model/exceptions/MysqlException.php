<?php
/**
 * User: anubis
 * Date: 05.09.13 14:04
 */

namespace aascms\model\exceptions;


use aascms\lib\PDOHelper;

class MysqlException extends Exception {

    public function __construct($query = '') {
        $message = sprintf("Mysql error %u: %s",
                           PDOHelper::getPDO()->errorCode(),
                           print_r(PDOHelper::getPDO()->errorInfo(), true));
        if(!empty($query)) {
            $message .= "\r\nSQL: ".$query;
        }
        parent::__construct($message);
    }

}
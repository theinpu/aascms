<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 20:52
 */

namespace aascms\app\commands;


use aascms\app\Command;

class ControlPanelCommand extends Command {

    public function __construct($method) {
        parent::__construct('ControlPanel', $method);
    }

}
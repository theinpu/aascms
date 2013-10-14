<?php
/**
 * User: anubis
 * Date: 13.10.13
 * Time: 20:49
 */

namespace aascms\app\controllers;

use aascms\lib\Config;

class ControlPanel extends GenericController {

    const Overview = 'overview';
    const Settings = 'settings';

    protected function template($template) {
        parent::template("controlPanel/".$template);
    }

    public function overview() {
        $this->addData(array("menu" => array(
            array(
                'image'       => 'settings',
                'title'       => 'Настройки',
                'description' => 'Настройки стайта',
                'link'        => '/cp/settings',
            ),
        )));
        $this->template("overview");
    }

    public function settings() {
        $cfg = Config::getConfigs();
        $groups = array();
        foreach($cfg as $key => $items) {
            $group = array('name' => $key);
            $group['items'] = array();
            foreach($items as $name => $item) {
                $group['items'][] = array(
                    'name'  => $name,
                    'value' => $item
                );
            }
            $groups[] = $group;
        }
        $this->addData(array('groups' => $groups));
        $this->template("settings");
    }

}
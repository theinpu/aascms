<?php
/**
 * User: anubis
 * Date: 14.10.13
 * Time: 1:41
 */

namespace aascms\app;

use aascms\app\commands\ControlPanelCommand;
use aascms\app\controllers\ControlPanel;
use Slim\Extras\Views\Twig;
use Slim\Slim;

class SimpleApplication extends Application {

    public function __construct() {
        parent::__construct(array(
                                 'templates.path' => '../templates',
                                 'debug'          => true,
                                 'mode'           => 'development'
                            ));
    }


    protected function initRoutes() {
        parent::initRoutes();
        $this->addGetCommand('/cp/', new ControlPanelCommand(ControlPanel::Overview));
    }

}
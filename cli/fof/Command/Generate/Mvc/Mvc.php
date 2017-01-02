<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Generate\Mvc;

use FOF30\Generator\Command\Command;
use FOF30\Generator\Command\Generate\Controller\Controller;
use FOF30\Generator\Command\Generate\Model\Model;
use FOF30\Generator\Command\Generate\View\View;

class Mvc extends Command
{
    public function execute()
    {
        $controller = new Controller($this->composer, $this->input);
        $controller->execute();

        $controller = new Model($this->composer, $this->input);
        $controller->execute();

        $controller = new View($this->composer, $this->input);
        $controller->execute();
    }
}
<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Generate\Controller;

use FOF30\Generator\Command\Command;
use FOF30\Factory\Scaffolding\Controller\Builder as ControllerBuilder;
use FOF30\Container\Container;

class Controller extends Command
{
    public function execute()
    {
        // Backend or frontend?
        $section = $this->input->get('frontend', false) ? 'site' : 'admin';
        $view    = $this->getViewName($this->input);

        // Let's force the use of the Magic Factory
        $container = Container::getInstance($this->component, array('factoryClass' => 'FOF30\\Factory\\MagicFactory'));
        $container->factory->setSaveScaffolding(true);

        // plural / singular
        $view = $container->inflector->singularize($view);

        $classname = $container->getNamespacePrefix($section).'Controller\\'.ucfirst($view);

        $scaffolding = new ControllerBuilder($container);
        $scaffolding->setSection($section);

        if(!$scaffolding->make($classname, $view))
        {
            throw new \RuntimeException("An error occurred while creating the Controller class");
        }
    }
}
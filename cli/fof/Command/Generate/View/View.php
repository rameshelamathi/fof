<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Generate\View;

use FOF30\Generator\Command\Command;
use FOF30\Factory\Scaffolding\View\Builder as ViewBuilder;
use FOF30\Container\Container;

class View extends Command
{
    public function execute()
    {
        // Backend or frontend?
        $section = $this->input->get('frontend', false) ? 'site' : 'admin';
        $view    = $this->getViewName($this->input);

        // Let's force the use of the Magic Factory
        $container = Container::getInstance($this->component, array('factoryClass' => 'FOF30\\Factory\\MagicFactory'));
        $container->factory->setSaveScaffolding(true);

        $view = $container->inflector->pluralize($view);

        $classname = $container->getNamespacePrefix($section).'View\\'.ucfirst($view).'\\Html';

        $scaffolding = new ViewBuilder($container);
        $scaffolding->setSection($section);

        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);

        if(!$scaffolding->make($classname, $view, 'html'))
        {
            throw new \RuntimeException("An error occurred while creating the View class");
        }
    }
}

<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Generate\Model;

use FOF30\Generator\Command\Command;
use FOF30\Factory\Scaffolding\Model\Builder as ModelBuilder;
use FOF30\Container\Container;

class Model extends Command
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

        $classname = $container->getNamespacePrefix($section).'Model\\'.ucfirst($view);

        $scaffolding = new ModelBuilder($container);
        $scaffolding->setSection($section);

        if(!$scaffolding->make($classname, $view))
        {
            throw new \RuntimeException("An error occurred while creating the Model class");
        }
    }
}
<?php

namespace FOF30\Generator\Command;

class Generate extends Command
{
    public function execute()
    {
        // Run some checks
        $this->doChecks();

        // Ok, I have to generate something, but... what?
        $class = $this->getClass();

        /** @var Command $generator */
        $generator = new $class($this->composer, $this->input);
        $generator->execute();

        $this->out('Class correctly created');
    }

    protected function doChecks()
    {
        $input = $this->input;

        // Do I have a name of the view?
        if(!$input->getString('name'))
        {
            throw new \RuntimeException("You have to specify the name of the view that will be used to generate FOF objects");
        }

        // Ok, did the user told me WHAT he wants?
        if($input->get('layout'))
        {
            return;
        }

        if($input->get('controller'))
        {
            return;
        }

        if($input->get('model'))
        {
            return;
        }

        if($input->get('view'))
        {
            return;
        }

        if($input->get('mvc'))
        {
            return;
        }

        throw new \RuntimeException("You have to specify **what** you want to create: model, view, controller or layout");
    }

    protected function getClass()
    {
        $input  = $this->input;
        $class  = 'FOF30\\Generator\\Command\\Generate';
        $return = '';

        $layout = strtolower($input->get('layout'));

        if(in_array($layout, array("1", 'item', 'default', 'form'), true))
        {
            $class .= '\\Layout';

            if($layout === "1")
            {
                $return = $class.'\\Layouts';
            }
            else
            {
                $return = $class.'\\'.ucfirst($layout).'Layout';
            }
        }
        else
        {
            $commands = array('controller', 'model', 'view', 'mvc');

            foreach($commands as $command)
            {
                if(!$input->get($command))
                {
                    continue;
                }

                $return = $class.'\\'.ucfirst($command).'\\'.ucfirst($command);

                break;
            }
        }

        if(!$return || !class_exists($return))
        {
            throw new \RuntimeException("Can not understand which object to generate. Please consult the documentation");
        }

        return $return;
    }
}
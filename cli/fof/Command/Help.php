<?php
/**
 * @package     FOF
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command;

use FOF30\Generator\Command\Command as Command;

class Help extends Command
{
	public function execute()
    {
		$this->out("");
		$this->out(str_repeat('-', 79));
		$this->out("FOF3 Generator Usage:");
		$this->out("fof init: Initialize a component");
        $this->out("fof setdevserver: Set the dev server location");
        $this->out("fof help: Show this help");
        $this->out("fof language: Generates the language files for the component");
        $this->out("fof generate --name <viewName> --controller [--frontend]: Generate the controller for the <viewName> view");
        $this->out("fof generate --name <viewName> --model [--frontend]: Generate the model for the <viewName> view");
        $this->out("fof generate --name <viewName> --view [--frontend]: Generate the view for the <viewName> view");
        $this->out("fof generate --name <viewName> --layout [--frontend]: Generate the all the 3 layout files (default, item, form) for the <viewName> view");
        $this->out("fof generate --name <viewName> --layout item [--frontend]: Generate the all the item layout file for the <viewName> view");
		$this->out("fof generate --name <viewName> --mvc [--frontend]: Generate the MVC for the <viewName> view");
		$this->out(str_repeat('-', 79));
		$this->out("");
	}
}

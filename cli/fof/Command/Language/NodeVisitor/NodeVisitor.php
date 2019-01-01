<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command\Language\NodeVisitor;

abstract class NodeVisitor
{
    /**
     * @var string
     */
    public $file;
    
    /**
     * @var array
     */
    public $results = [];
    /**
     * Starts traversing an array of files.
     *
     * @param  array $files
     * @return array
     */
    abstract public function traverse(array $files);
   
    /**
     * @param  string $name
     * @return string
     */
    protected function loadTemplate($name)
    {
        return $this->file = $name;
    }
}
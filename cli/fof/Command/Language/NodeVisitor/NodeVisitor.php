<?php

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
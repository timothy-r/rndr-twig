<?php namespace Ace;

/**
 * @author timrodger
 * Date: 18/03/15
 */
class TemplateFinder
{
    private $root;

    private $extension;

    public function __construct($root, $extenstion)
    {
        $this->root = $root;
        $this->extension = $extenstion;
    }

    public function find($path)
    {
        // try to find a template file below root
        $file = sprintf('%s/%s.%s', $this->root, $path, $this->extension);
        // $path wont contain the extension
        if (is_readable($file)){
            return $file;
        }
        return null;
    }
}
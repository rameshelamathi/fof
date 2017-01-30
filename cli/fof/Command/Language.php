<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Generator\Command;

use FOF30\Container\Container;
use JText;

class Language extends Command
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $baseString;

    /**
     * Executes the command
     */
    public function execute()
    {
        // Backend or frontend?
        $frontend = $this->input->get('frontend', false) ? true : false;
        $backend = $this->input->get('backend', false) ? true : false;

        // Nothing specified => both sides
        if (!$frontend && !$backend) {
            $frontend = true;
            $backend = true;
        }

        $sides = [];
        if ($frontend) {
            $sides[] = 'site';
        }

        if ($backend) {
            $sides[] = 'admin';
        }

        $this->container = Container::getInstance($this->component);
        $this->baseString = strtoupper($this->component . '_');

        $languages = array_keys(\JLanguage::getKnownLanguages());

        foreach ($sides as $side) {
            $strings = $this->getStrings($side);

            foreach ($languages as $lang) {
                $this->saveStrings($strings, $lang, $side);
            }
        }
    }

    /**
     * Saves the language strings, merged with any old ones, to a Joomla! INI language file
     *
     * @param $newStrings   array   The associative array of new strings to be save
     * @param $language     string  The language for which we're currently saving the strings
     * @param $side         string  Site or Admin side?
     */
    protected function saveStrings($newStrings = [], $language = 'en-GB', $side = 'admin')
    {
        // If no filename is defined, get the component's language definition filename
        $basePath = $side == 'admin' ? JPATH_ADMINISTRATOR : JPATH_SITE;

        $path = \JLanguage::getLanguagePath($basePath, $language);

        $targetFilename = $path . '/' . $language . '.' . $this->component . '.ini';

        // Try to load the existing language file
        $strings = array();

        if (@file_exists($targetFilename)) {
            $contents = file_get_contents($targetFilename);
            $contents = str_replace('_QQ_', '"\""', $contents);
            $strings = @parse_ini_string($contents);
        } else {
            \JFile::write($targetFilename, '');
        }

        foreach ($newStrings as $k => &$v) {
            $v = str_ireplace($this->baseString, '', $k);
            $parts = explode("_", $v);

            foreach ($parts as &$part) {
                $part = ucfirst(strtolower($part)); // TODO: find a better default value
            }

            $v = implode(" ", $parts);
        }

        if (!is_array($strings)) {
            return;
        }

        // Merge the new strings with the existing ones. Priority to the existing ones, of course
        $strings = array_merge($newStrings, $strings);

        // Create the INI file
        $iniFile = '';

        foreach ($strings as $k => $v) {
            $iniFile .= strtoupper(trim($k)) . '="' . str_replace('"', '"_QQ_"', trim($v)) . "\"\n";
        }

        // Save it
        \JFile::write($targetFilename, $iniFile);
    }

    /**
     * @param string $side Site or admin
     * @return array
     */
    protected function getStrings($side = 'admin')
    {
        $path = $this->container->frontEndPath;

        if ($side == 'admin') {
            $path = $this->container->backEndPath;
        }

        $phpResults = $this->getStringsFromPhpFiles($path);
        $xmlResults = $this->getStringsFromXmlFiles($path);

        $results = array_merge($xmlResults, $phpResults);

        // Get list of views, and generate any title_ key
        $views = $this->getViews($side);
        foreach ($views as $view) {
            $results = array_merge($results, $this->getViewTitles($view));
        }

        return $results;
    }

    /**
     * Get the list of views that are present
     *
     * @param   string  $side   site or admin
     * @return  array
     */
    protected function getViews($side = 'admin')
    {
        $views = array();

        $componentPaths = $this->container->platform->getComponentBaseDirs($this->container->componentName);
        $searchPath = $componentPaths[$side] . '/View';
        $filesystem = $this->container->filesystem;

        $allFolders = $filesystem->folderFolders($searchPath);

        if (!empty($allFolders)) {
            foreach ($allFolders as $folder) {
                $view = $folder;

                // View already added
                if (in_array($this->container->inflector->pluralize($view), $views)) {
                    continue;
                }

                // Do we have a 'skip.xml' file in there?
                $files = $filesystem->folderFiles($searchPath . '/' . $view, '^skip\.xml$');

                if (!empty($files)) {
                    continue;
                }

                // Do we have extra information about this view? (ie. ordering)
                $meta = $filesystem->folderFiles($searchPath . '/' . $view, '^metadata\.xml$');

                // Not found, do we have it inside the plural one?
                if (!$meta) {
                    $plural = $this->container->inflector->pluralize($view);

                    if (in_array($plural, $allFolders)) {
                        $view = $plural;
                    }
                }

                $view = $this->container->inflector->pluralize($view);
                $views[] = $view;
            }
        }

        return $views;
    }

    /**
     * Get the FOF default language keys for titles, actions and errors
     * @param   string  $view   The view for which we're generating the keys for
     * @return  array
     */
    protected function getViewTitles($view)
    {
        $titles = [];

        // VIEW MAIN TITLES
        $prefix = 'TITLE';
        foreach ([false, '_EDIT', '_ADD'] as $suffix) {
            $titles = array_merge($titles, $this->getViewTitle($prefix, $view, $suffix));
        }

        // VIEW ACTIONS LABELS
        $prefix = 'LBL';
        foreach (['SAVED', 'DELETED', 'COPIED'] as $suffix) {
            $titles = array_merge($titles, $this->getViewTitle($prefix, $this->container->inflector->singularize($view), $suffix));
        }

        // VIEW ERRORS
        $prefix = 'ERR';
        $titles = array_merge($titles, $this->getViewTitle($prefix, $view, 'NOT_FOUND'));

        return $titles;
    }

    /**
     * Generate the view title, same way that FOF does
     *
     * @param $prefix
     * @param $view
     * @param bool $suffix
     * @return array
     */
    protected function getViewTitle($prefix, $view, $suffix = false)
    {
        $key = strtoupper($this->container->componentName) . '_' . $prefix . '_' . strtoupper($view);

        if ($suffix) {
            $key .= '_' . $suffix;
        }

        //Do we have a translation for this key?
        if (strtoupper(JText::_($key)) == $key) {
            $altview = $this->container->inflector->isPlural($view) ? $this->container->inflector->singularize($view) : $this->container->inflector->pluralize($view);
            $key2 = strtoupper($this->container->componentName) . '_TITLE_' . strtoupper($altview);

            // Maybe we have for the alternative view?
            if (strtoupper(JText::_($key2)) == $key2) {
                // Nope, let's use the raw name
                $name = ucfirst($view);
            } else {
                $name = JText::_($key2);
            }
        } else {
            $name = JText::_($key);
        }

        return [$key => $name];
    }

    /**
     * @param $path
     * @return array
     */
    protected function getStringsFromPhpFiles($path)
    {
        $phps = \JFolder::files($path, $filter = '.php', $recurse = true, $full = true);

        // Visit each php file for JText calls
        $visitor = new Language\NodeVisitor\PhpNodeVisitor();

        return $visitor->traverse($phps);
    }

    /**
     * Goes through any xml file in the component (most likely params, fof views, layout and menu xml files
     * and returns an associative array of the matching language strings
     *
     * @param   string  $path The path in which we should search the xml files
     * @return array
     */
    protected function getStringsFromXmlFiles($path)
    {
        $xmls = \JFolder::files($path, $filter = '.xml', $recurse = true, $full = true);

        $results = [];

        // Visit each xml file for strings starting
        foreach ($xmls as $file) {
            $xml = \JFile::read($file);

            try {
                $xml = @new \SimpleXMLElement($xml);
                $results = array_merge($results, $this->parseXml($xml, $file));
            } catch (\Exception $e) {
                // Ignore eventual xml errors.
            }
        }

        return $results;
    }

    /**
     * Parse an xml file for strings matching our component
     *
     * @param \SimpleXMLElement $xml
     * @param $file
     * @return array
     */
    protected function parseXml($xml, $file)
    {
        $results = array();

        foreach ($xml->attributes() as $key => $value) {
            if (stripos($value, $this->baseString) !== false) {
                $results[(string)$value][] = ['file' => $file];
            }
        }

        if ($xml->count() > 0) {
            foreach ($xml->children() as $child) {
                $results = array_merge($results, $this->parseXml($child, $file));
            }
        } else {
            if (stripos($xml, $this->baseString) !== false) {
                $results[(string)$xml][] = ['file' => $file];
            }
        }

        return $results;
    }
}
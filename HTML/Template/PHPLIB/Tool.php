<?php

/**
* Command line tool to use the HTML_Template_PHPLIB validator and generator.
*
* @category HTML
* @package HTML_Template_PHPLIB
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
* @link     http://pear.php.net/package/HTML_Template_PHPLIB
*/
class HTML_Template_PHPLIB_Tool
{
    function HTML_Template_PHPLIB_Tool($args)
    {
        $strAction = $this->getAction($args);
        $this->{'do' . ucfirst($strAction)}($args);        
    }//function HTML_Template_PHPLIB_Tool($args)
    
    
    
    function getAction(&$args)
    {
        if (count($args) == 0) {
            return 'help';
        }
        $arg = array_shift($args);
        switch ($arg) {
        case 'v':
        case 'validate':
        case '-v':
        case '--validate':
            return 'validate';
        default:
            return 'help';
        }
    }//function getAction(&$args)
    
    
    
    function dieHard($strMessage, $nExitCode)
    {
        echo $strMessage;
        exit($nExitCode);
    }//function dieHard($strMessage, $nExitCode)
    
    
    
    function doHelp()
    {
        echo <<<EOT
Usage: html_template_phplibtool action parameters

Tool to validate and work with HTML templates

mode: (- and -- are optional)
 h,  help      Show this help screen
 v,  validate  Validate a template file

EOT;
    }//function doHelp()
    
    
    
    function doValidate($args)
    {
        if (count($args) == 0) {
            $this->dieHard("No template files to validate\n", 1);
        }
        
        require_once 'HTML/Template/PHPLIB/Validator.php';
        $nError = 0;
        foreach ($args as $file) {
            if (file_exists($file)) {
                $arErrors = HTML_Template_PHPLIB_Validator::validate($file);
                if ($arErrors === true) {
                    echo 'No errors found in ' . $file . "\n";
                    $nError =  0;
                } else if ($arErrors === false) {
                    echo 'Some unexpected error in ' . $file . "\n";
                    $nError =  3;
                } else {
                    echo count($arErrors) . ' errors in ' . $file . "\n";
                    foreach ($arErrors as $arError) {
                        echo ' Line #' . $arError['line'] . ': ' . $arError['message'] . "\n";
                    }
                    $nError = 10;
                }
            } else {
                echo 'File does not exist: ' . $file . "\n";
                $nError = 4;
            }
        }
        $this->dieHard('', $nError);
    }//function doValidate($args)



    /**
    * Start the tool
    *
    * @static
    */
    function run()
    {
        $args = $GLOBALS['argv'];
        array_shift($args);
        new HTML_Template_PHPLIB_Tool($args);        
    }//function run()

}//class HTML_Template_PHPLIB_Tool

?>
<?php
/**
 * Additional tools for HTML_Template_PHPLIB
 *
 * PHP Versions 4 and 5
 *
 * @category HTML
 * @package  HTML_Template_PHPLIB
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version  CVS: $Id$
 * @link     http://pear.php.net/package/HTML_Template_PHPLIB
 */
require_once 'HTML/Template/PHPLIB/Helper.php';

/**
* Generates code to be used with templates
*
* @category HTML
* @package  HTML_Template_PHPLIB
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
* @link     http://pear.php.net/package/HTML_Template_PHPLIB
*/
class HTML_Template_PHPLIB_Generator
{

    /**
    * Creates the code to use a given template file
    *
    * @param string $strFile    Template file
    * @param string $strTplName Template reference name
    * @param string $strPrefix  Prefix to prepend before the code
    *
    * @return string PHP code
    */
    function getCodeBlockDefinition(
        $strFile, $strTplName = null, $strPrefix = '$tpl'
    ) {
        $arBlocks = HTML_Template_PHPLIB_Generator::getBlocks(
            HTML_Template_PHPLIB_Helper::getLines($strFile)
        );

        if ($strTplName === null) {
            $strTplName
                = HTML_Template_PHPLIB_Generator::getTemplateNameFromFilename(
                    $strFile
                );
        }

        $nl    = "\r\n";
        $code  = '';
        $code .= $strPrefix . ' = new HTML_Template_PHPLIB();' . $nl;
        $code .= HTML_Template_PHPLIB_Generator::getCodeBlock(
            $arBlocks, $strTplName, $strPrefix
        );
        $code .= $nl;
        $code .= '//TODO: do something with the code' . $nl;
        $code .= $nl;

        $code .= $strPrefix . '->finish('
            . trim($strPrefix) . "->parse('TMP', '" . $strTplName . "'));"
            . $nl;

        return $code;
    }//function getCodeBlockDefinition($strFile, $strTplName = null, ..)



    /**
    * Creates the PHP code for the given array of blocks.
    *
    * @param array  $arBlocks   Array of blocks, see getBlocks()
    * @param string $strTplName Template reference name
    * @param string $strPrefix  Prefix to prepend before the code
    *
    * @return string PHP code
    */
    function getCodeBlock($arBlocks, $strTplName, $strPrefix = '$tpl')
    {
        $nl   = "\r\n";
        $code = '';
        foreach ($arBlocks as $arBlock) {
            if (count($arBlock['sub']) > 0) {
                $code .= HTML_Template_PHPLIB_Generator::getCodeBlock(
                    $arBlock['sub'], $strTplName, $strPrefix
                );
            }
            $code .= $strPrefix . "->setBlock('" . $strTplName . "','"
                    . $arBlock['name'] . "', '"
                    . $arBlock['name'] . "_ref');" . $nl;
        }

        return $code;
    }//function getCodeBlock($arBlocks, $strTplName, $strPrefix = '$tpl')



    /**
    * Returns an array of blocks in the given template code.
    * The array values are array with a key "name" and
    *  "sub", an array of nested blocks.
    *
    * @param array $arLines Template code lines
    *
    * @return array Array of blocks
    *
    * @static
    */
    function getBlocks($arLines)
    {
        $arBlocks = array();
        $arRefs   = array();
        $strRegex = '/<!--\s+(BEGIN|END)\s+([a-zA-Z0-9_]*)\s+-->/';
        foreach ($arLines as $strLine) {
            $arMatches = array();
            if (!preg_match($strRegex, $strLine, $arMatches)) {
                continue;
            }
            $strType      = $arMatches[1];
            $strBlockName = $arMatches[2];
            if ($strType == 'BEGIN') {
                $arBlock = array(
                    'name' => $strBlockName,
                    'sub'  => array()
                );
                if (count($arRefs) == 0) {
                    $arBlocks[$arBlock['name']] = $arBlock;
                    $arRefs[$strBlockName]      = &$arBlocks[$arBlock['name']];
                } else {
                    end($arRefs);
                    $strOldBlock = key($arRefs);

                    $arRefs[$strOldBlock]['sub'][$strBlockName] = $arBlock;
                    $arRefs[$strBlockName]
                        =& $arRefs[$strOldBlock]['sub'][$strBlockName];
                }
            } else {
                unset($arRefs[$strBlockName]);
            }
        }

        return $arBlocks;
    }//function getBlocks($arLines)



    /**
    * Creates a name that can be used as handle for a template,
    *  from the given file name.
    *
    * @param string $strFile File name
    *
    * @return string Template name
    */
    function getTemplateNameFromFilename($strFile)
    {
        $strTplName = basename($strFile);
        //remove extension
        $nDotPos = strpos($strTplName, '.');
        if ($nDotPos !== false) {
            $strTplName = substr($strTplName, 0, $nDotPos);
        }

        return $strTplName;
    }//function getTemplateNameFromFilename($strFile)

}//class HTML_Template_PHPLIB_Generator

?>
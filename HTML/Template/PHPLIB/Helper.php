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

/**
* Helper methods for the HTML_Template_PHPLIB tool
*
* @category HTML
* @package  HTML_Template_PHPLIB
* @author   Christian Weiske <cweiske@php.net>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
* @link     http://pear.php.net/package/HTML_Template_PHPLIB
*/
class HTML_Template_PHPLIB_Helper
{

    /**
    * Returns an array with all lines of the text.
    * Extracts it from the file or the text
    *
    * @param string $strFile    File name
    * @param string $strContent Template code
    *
    * @return array Array with text lines, without trailing newlines,
    *                false when both are null
    *
    * @static
    */
    function getLines($strFile = null, $strContent = null)
    {
        if ($strContent !== null) {
            $arLines = HTML_Template_PHPLIB_Helper::splitLines($strContent);
        } else if ($strFile !== null) {
            $arLines = file($strFile, FILE_IGNORE_NEW_LINES);
        } else {
            //all null?
            return false;
        }

        return $arLines;
    }//function getLines($strFile = null, $strContent = null)



    /**
    * Splits the content into single lines and returns
    *  the array.
    * Similar to file(), but works directly on the content
    *  instead of the file name.
    *
    * @param string $strContent File content to be split into lines
    *
    * @return array Array of line strings without trailing newlines
    *
    * @static
    */
    function splitLines($strContent)
    {
        return explode(
            "\n", str_replace(array("\r\n", "\r"), "\n", $strContent)
        );
    }//function splitLines($strContent)

}//class HTML_Template_PHPLIB_Helper

?>
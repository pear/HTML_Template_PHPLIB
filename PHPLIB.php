<?php
// vim: set expandtab tabstop=4 shiftwidth=4:
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Bjoern Schotte <bjoern@rent-a-phpwizard.de>                 |
// |          Martin Jansen <mj@php.net> (PEAR conformance)               |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once "PEAR.php";

/**
 * Converted PHPLIB Template class
 *
 * For those who want to use PHPLIB's fine template class,
 * here's a PEAR conforming class with the original PHPLIB
 * template code from phplib-stable CVS. Original author
 * was Kristian Koehntopp <kris@koehntopp.de>
 *
 * @author  Bjoern Schotte <bjoern@rent-a-phpwizard.de>
 * @author  Martin Jansen <mj@php.net> (PEAR conformance)
 * @version 1.0
 */
class Template_PHPLIB
{
    /**
     * If set, echo assignments
     * @var bool
     */
    var $debug     = false;

    /**
     * $file[handle] = "filename";
     * @var array
     */
    var $file  = array();

    /**
     * Relative filenames are relative to this pathname
     * @var string
     */
    var $root   = "";

    /*
     * $_varKeys[key] = "key"
     * @var array
     */
    var $_varKeys = array();
    
    /**
     * $_varVals[key] = "value";
     * @var array
     */
    var $_varVals = array();

    /**
     * "remove"  => remove undefined variables
     * "comment" => replace undefined variables with comments
     * "keep"    => keep undefined variables
     * @var string
     */
    var $unknowns = "remove";
  
    /**
     * "yes" => halt, "report" => report error, continue, "no" => ignore error quietly
     * @var string
     */
    var $haltOnError  = "yes";
  
    /**
     * The last error message is retained here
     * @var string
     * @see halt
     */
    var $_lastError     = "";


    /**
     * Constructor
     *
     * @access public
     * @param  string template root directory
     * @param  string how to handle unknown variables
     */
    function Template_PHPLIB($root = ".", $unknowns = "remove")
    {
        $this->setRoot($root);
        $this->setUnknowns($unknowns);
    }

    /**
     * Sets the template directory
     *
     * @access public
     * @param  string new template directory
     * @return bool
     */
    function setRoot($root)
    {
        if (!is_dir($root)) {
            $this->halt("setRoot: $root is not a directory.");
            return false;
        }
    
        $this->root = $root;
    
        return true;
    }

    /**
     * What to do with unknown variables
     *
     * three possible values:
     *
     * - "remove" will remove unknown variables
     *   (don't use this if you define CSS in your page)
     * - "comment" will replace undefined variables with comments
     * - "keep" will keep undefined variables as-is
     *
     * @access public
     * @param  string unknowns
     */
    function setUnknowns($unknowns = "keep")
    {
        $this->unknowns = $unknowns;
    }

    /**
     * Set appropriate template files
     *
     * With this method you set the template files you want to use.
     * Either you supply an associative array with key/value pairs
     * where the key is the handle for the filname and the value
     * is the filename itself, or you define $handle as the file name
     * handle and $filename as the filename if you want to define only
     * one template.
     *
     * @access public
     * @param  mixed handle for a filename or array with handle/name value pairs
     * @param  string name of template file
     * @return bool
     */
    function setFile($handle, $filename = "")
    {
        if (!is_array($handle)) {
    
            if ($filename == "") {
                $this->halt("setFile: For handle $handle filename is empty.");
                return false;
            }
      
            $this->file[$handle] = $this->_filename($filename);
      
        } else {
    
            reset($handle);
            while (list($h, $f) = each($handle)) {
                $this->file[$h] = $this->_filename($f);
            }
        }
    }

    /**
     * Set a block in the appropriate template handle
     *
     * By setting a block like that:
     *
     * &lt;!-- BEGIN blockname --&gt;
     * html code
     * &lt;!-- END blockname --&gt;
     *
     * you can easily do repeating HTML code, i.e. output
     * database data nice formatted into a HTML table where
     * each DB row is placed into a HTML table row which is
     * defined in this block.
     * It extracts the template $handle from $parent and places
     * variable {$name} instead.
     *
     * @access public
     * @param  string parent handle
     * @param  string block name handle
     * @param  string variable substitution name
     */
    function setBlock($parent, $handle, $name = "")
    {
        if (!$this->_loadFile($parent)) {
            $this->halt("subst: unable to load $parent.");
            return false;
        }
    
        if ($name == "") {
            $name = $handle;
        }

        $str = $this->getVar($parent);
        $reg = "/<!--\s+BEGIN $handle\s+-->(.*)\n\s*<!--\s+END $handle\s+-->/sm";
        preg_match_all($reg, $str, $m);
        $str = preg_replace($reg, "{" . "$name}", $str);

        $this->setVar($handle, $m[1][0]);
        $this->setVar($parent, $str);
    }

    /**
     * Set corresponding substitutions for placeholders
     *
     * @access public
     * @param  string name of a variable that is to be defined or an array of variables with value substitution as key/value pairs
     * @param  string value of that variable
     */
    function setVar($varname, $value = "")
    {
        if (!is_array($varname)) {

            if (!empty($varname))
                if ($this->debug) print "scalar: set *$varname* to *$value*<br>\n";

            $this->_varKeys[$varname] = "/".$this->_varname($varname)."/";
            $this->_varVals[$varname] = $value;

        } else {
            reset($varname);

            while (list($k, $v) = each($varname)) {
                if (!empty($k))
                    if ($this->debug) print "array: set *$k* to *$v*<br>\n";

                $this->_varKeys[$k] = "/".$this->_varname($k)."/";
                $this->_varVals[$k] = $v;
            }
        }
    }

    /**
     * Substitute variables in handle $handle
     *
     * @access public
     * @param  string name of handle
     * @return mixed string substituted content of handle
     */
    function subst($handle)
    {
        if (!$this->_loadFile($handle)) {
            $this->halt("subst: unable to load $handle.");
            return false;
        }

        $str = $this->getVar($handle);
        $str = @preg_replace($this->_varKeys, $this->_varVals, $str);

        return $str;
    }
  
    /**
     * Same as subst but printing the result
     *
     * @access  public
     * @brother subst
     * @param   string handle of template
     * @return  bool always false
     */
    function pSubst($handle)
    {
        print $this->subst($handle);
        return false;
    }

    /**
     * Parse handle into target
     *
     * Parses handle $handle into $target, eventually
     * appending handle at $target if $append is defined
     * as TRUE.
     *
     * @access public
     * @param  string target handle to parse into
     * @param  string which handle should be parsed
     * @param  boolean append it to $target or not?
     * @return string parsed handle
     */
    function parse($target, $handle, $append = false)
    {
        if (!is_array($handle)) {
            $str = $this->subst($handle);

            if ($append) {
                $this->setVar($target, $this->getVar($target) . $str);
            } else {
                $this->setVar($target, $str);
            }
        } else {
            reset($handle);

            while (list($i, $h) = each($handle)) {
                $str = $this->subst($h);
                $this->setVar($target, $str);
            }
        }

        return $str;
    }

    /**
     * Same as parse, but printing it.
     *
     * @access  public
     * @brother parse
     * @param   string target to parse into
     * @param   string handle which should be parsed
     * @param   should $handle be appended to $target?
     * @return  bool
     */
    function pParse($target, $handle, $append = false)
    {
        print $this->parse($target, $handle, $append);
        return false;
    }
  
    /**
     * Return all defined variables and their values
     *
     * @access public
     * @return array with all defined variables and their values
     */
    function getVars()
    {
        reset($this->_varKeys);

        while (list($k, $v) = each($this->_varKeys)) {
            $result[$k] = $this->_varVals[$k];
        }

        return $result;
    }

    /**
     * Return one or more specific variable(s) with their values.
     *
     * @access public    
     * @param  mixed array with variable names or one variable name as a string
     * @return mixed array of variable names with their values or value of one specific variable
     */
    function getVar($varname)
    {
        if (!is_array($varname)) {
            return $this->_varVals[$varname];
        } else {
            reset($varname);
    
            while (list($k, $v) = each($varname)) {
                $result[$k] = $this->_varVals[$k];
            }

            return $result;
        }
    }
  
    /**
     * Get undefined values of a handle
     *
     * @access public
     * @param  string handle name
     * @return mixed  false if an error occured or the undefined values
     */
    function getUndefined($handle)
    {
        if (!$this->_loadFile($handle)) {
            $this->halt("getUndefined: unable to load $handle.");
            return false;
        }
    
        preg_match_all("/\{([^}]+)\}/", $this->getVar($handle), $m);
        $m = $m[1];
        if (!is_array($m)) {
            return false;
        }

        reset($m);
        while (list($k, $v) = each($m)) {
            if (!isset($this->_varKeys[$v])) {
                $result[$v] = $v;
            }
        }
    
        if (count($result)) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Finish string
     *
     * @access public
     * @param  string string to finish
     * @return finished, i.e. substituted string
     */
    function finish($str)
    {
        switch ($this->unknowns) {
            case "keep":
                break;
      
            case "remove":
                $str = preg_replace('/{[^ \t\r\n}]+}/', "", $str);
                break;

            case "comment":
                $str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template $handle: Variable \\1 undefined -->", $str);
                break;
        }

        return $str;
    }

    /**
     * Print variable to the browser
     *
     * @access public
     * @param  string name of variable to print
     */
    function p($varname)
    {
        print $this->finish($this->getVar($varname));
    }

    /**
     * Get finished variable
     *
     * @access public public
     * @param  string variable to get
     * @return string string with finished variable
     */
    function get($varname)
    {
        return $this->finish($this->getVar($varname));
    }

    /**
     * Complete filename
     *
     * Complete filename, i.e. testing it for slashes
     *
     * @access private
     * @param  string filename to be completed
     * @return string completed filename
     */
    function _filename($filename)
    {
        if (substr($filename, 0, 1) != "/") {
            $filename = $this->root."/".$filename;
        }

        if (!file_exists($filename)) {
            $this->halt("filename: file $filename does not exist.");
        }

        return $filename;
    }

    /**
     * Protect a replacement variable
     *
     * @access private
     * @param  string name of replacement variable
     * @return string replaced variable
     */
    function _varname($varname)
    {
        return preg_quote("{".$varname."}");
    }

    /**
     * load file defined by handle if it is not loaded yet
     *
     * @access private
     * @param  string handle
     * @return bool   FALSE if error, true if all is ok
     */
    function _loadFile($handle)
    {
        if (isset($this->_varKeys[$handle]) and !empty($this->_varVals[$handle])) {
            return true;
        }

        if (!isset($this->file[$handle])) {
            $this->halt("loadfile: $handle is not a valid handle.");
          return false;
        }

        $filename = $this->_filename($this->file[$handle]);
        $str = implode("", @file($filename));

        if (empty($str)) {
            $this->halt("loadfile: While loading $handle, $filename does not exist or is empty.");
            return false;
        }

        $this->setVar($handle, $str);

        return true;
    }

    /**
     * Error function. Halt template system with message to show
     *
     * @access public
     * @param  string message to show
     * @return bool
     */
    function halt($msg)
    {
        $this->_lastError = $msg;

        if ($this->haltOnError != "no") {
            return $this->haltMsg($msg);
        }

        /**
        if ($this->haltOnError == "yes")
            die("<b>Halted.</b>");
        */

        return false;
    }
  
    /**
     * printf error message to show
     *
     * @access public
     * @param  string message to show
     * @return object PEAR error object
     */
    function haltMsg($msg)
    {
        return new PEAR_ERROR(sprintf("<b>Template Error:</b> %s<br>\n", $msg));
    }
}
?>

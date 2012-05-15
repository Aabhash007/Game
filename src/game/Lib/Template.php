<?php
/*
 * Template.php -
 * Copyright (c) 2011-2012  David Unger
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** Set Namespace **/
namespace Lib;

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

################################################################################
class Template {
  private $file;
  private $showRresource;

  private $vars;
  private $twig;
  private $template;

  public function __construct($path='') {
    if (empty($path)) {
      $path = $_SESSION['player']->getTemplatePath();
    }

    // include and register Twig auto-loader
    include 'include/Twig/Autoloader.php';
    \Twig_Autoloader::register();

    try {
      // specify where to look for templates
      $loader = new \Twig_Loader_Filesystem($path);

      // initialize Twig environment
      $this->twig = new \Twig_Environment($loader, array(
        'cache'       => (TEMPLATE_CACHE) ? $path . 'cache' : false,
        'auto_reload' => TEMPLATE_RELOAD,
        'debug'       => DEBUG
      ));
    } catch (Exception $e) {
      if (DEBUG) {
        die ('ERROR: ' . $e->getMessage());
      } else {
        die ('ERROR: Template konnte nicht geladen werden!');
      }
    }
    
    $this->vars = array();
    $this->showRresource = true;
  }
  
  public function addVars($vars) {
    $this->vars = array_merge($this->vars, $vars);
  }

  public function addVar($name, $var) {
    $this->vars[$name] = $var;
  }

  public function getVar($name) {
    return (isset($this->vars[$name])) ? $this->vars[$name] : NULL;
  }

  public function throwError($msg) {
    $this->file = 'message.tmpl';
    $this->addVar('msg', $msg);
  }

  public function setFile($file) {
    $this->file = $file;
  }

  public function setShowRresource($flag) {
    $this->showRresource = $flag;
  }

  public function getShowRresource() {
    return $this->showRresource;
  }

  public function render() {
    try {
      if (empty($this->file)) {
        $this->file = 'message.tmpl';
      }

      // set template file
      $this->template = $this->twig->loadTemplate($this->file);

      // parse and output template
      echo $this->template->render($this->vars);
    } catch (Exception $e) {
      if (DEBUG) {
        die ('ERROR: ' . $e->getMessage());
      } else {
        die ('ERROR: Template konnte nicht geladen werden!');
      }
    }
  }
}

?>
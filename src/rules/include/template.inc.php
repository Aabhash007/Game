<?php
/*
 * template.inc.php -
 * Copyright (c) 2011  David Unger
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

class Template {
  private $file;
  private $showRresource;

  private $vars;
  private $twig;
  private $template;

  public function __construct($path='') {
    global $config;

    // include and register Twig auto-loader
    include 'Twig/Autoloader.php';
    Twig_Autoloader::register();

    try {
      // specify where to look for templates
      $loader = new Twig_Loader_Filesystem('templates/');

      // initialize Twig environment
      $this->twig = new Twig_Environment($loader, array(
        'cache'       => (TEMPLATE_CACHE) ? 'templates/cache' : false,
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
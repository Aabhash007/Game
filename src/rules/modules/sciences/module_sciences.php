<?php
/*
 * module_science.php - 
 * Copyright (c) 2003  OGP-Team
 * Copyright (c) 2011  David Unger
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

function sciences_getSelector() {

  $sciences = array();
  foreach ($GLOBALS['scienceTypeList'] AS $key => $value) {
    if (!$value->nodocumentation) {
      $scienceID = request_var('sciencesID', 0);

      $temp = array(
        'value'       => $value->scienceID,
        'description' => lib_shorten_html($value->name, 20)
      );

      if (isset($_REQUEST['sciencesID']) && $scienceID == $value->scienceID) {
        $temp['selected'] = 'selected="selected"';
      }

      $sciences[] = $temp;
    }
  }
  usort($sciences, "descriptionCompare");

  return $sciences;
}

function sciences_getContent() {
  global $template;

  // open template
  $template->setFile('science.tmpl');

  $id = request_var('sciencesID', 0);
  if (!isset($GLOBALS['scienceTypeList'][$id]) || $GLOBALS['scienceTypeList'][$id]->nodocumentation) {
    $science = $GLOBALS['scienceTypeList'][0];
  } else {
    $science = $GLOBALS['scienceTypeList'][$id];
  }

  $resourceCost = array();
  foreach ($science->resourceProductionCost as $key => $value) {
    if ($value != "" && $value != "0") {
      array_push($resourceCost, array(
        'dbFieldName' => $GLOBALS['resourceTypeList'][$key]->dbFieldName,
        'name'        => $GLOBALS['resourceTypeList'][$key]->name,
        'amount'      => formula_parseToReadable($value)
      ));
    }
  }

  $unitCost = array();
  foreach ($science->unitProductionCost as $key => $value) {
    if ($value != "" && $value != "0") {
      array_push($unitCost, array(
        'dbFieldName' => $GLOBALS['unitTypeList'][$key]->dbFieldName,
        'name'        => $GLOBALS['unitTypeList'][$key]->name,
        'amount'      => formula_parseToReadable($value)
      ));
    }
  }

  $defenseCost = array();
  foreach ($science->defenseProductionCost as $key => $value) {
    if ($value != "" && $value != "0") {
      array_push($defenseCost, array(
        'dbFieldName' => $GLOBALS['defenseSystemTypeList'][$key]->dbFieldName,
        'name'        => $GLOBALS['defenseSystemTypeList'][$key]->name,
        'amount'      => formula_parseToReadable($value)
      ));
    }
  }

  $buildingCost = array();
  foreach ($science->buildingProductionCost as $key => $value) {
    if ($value != "" && $value != "0") {
      array_push($externalCost, array(
        'dbFieldName' => $GLOBALS['buildingTypeList'][$key]->dbFieldName,
        'name'        => $GLOBALS['buildingTypeList'][$key]->name,
        'amount'      => formula_parseToReadable($value)
      ));
    }
  }

  $moreCost = array_merge($unitCost, $defenseCost, $buildingCost);
  $template->addVars(array(
    'name'           => $science->name,
    'description'    => $science->description,
    'maximum'        => formula_parseToReadable($science->maxLevel),
    'productionTime' => "(".formula_parseToReadable($science->productionTimeFunction).")*".SCIENCE_TIME_BASE_FACTOR." (in Sekunden)",
    'dbFieldName'    => $science->dbFieldName,
    'resource_cost'  => $resourceCost,
    'dependencies'   => rules_checkDependencies($science),
    'more_cost'      => (sizeof($moreCost)) ? $moreCost : false,
  ));
}
?>
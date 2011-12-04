<?php
/*
 * defense.html.php -
 * Copyright (c) 2004  OGP Team
 * Copyright (c) 2011  David Unger
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');


################################################################################

/**
 *
 */

function defense_showProperties($defenseID, $cave, $method) {
  global $template;

  // first check whether that defense should be displayed...
  $defense = $GLOBALS['defenseSystemTypeList'][$defenseID];
  $maxLevel = round(eval('return '.formula_parseToPHP("{$defense->maxLevel};", '$cave')));
  $maxLevel = ($maxLevel < 0) ? 0 : $maxLevel;

  if (!$defense || ($defense->nodocumentation && !$cave[$defense->dbFieldName] && rules_checkDependencies($defense, $cave) !== TRUE)) {
    $defense = current($GLOBALS['defenseSystemTypeList']);
  }

  // open template
  if ($method == 'ajax') {
    $shortVersion = true;
    $template->setFile('defenseDetailAjax.tmpl');
  }
  else {
    $shortVersion = false;
    $template->setFile('defenseDetail.tmpl');    
  }

  $currentlevel = $cave[$defense->dbFieldName];
  $levels = array();
  for ($level = $cave[$defense->dbFieldName], $count = 0;
       $level < $maxLevel && $count < ($shortVersion ? 3 : 10);
       ++$count, ++$level, ++$cave[$defense->dbFieldName]) {

    $duration = time_formatDuration(eval('return ' . formula_parseToPHP($defense->productionTimeFunction.";",'$cave')) * DEFENSESYSTEM_TIME_BASE_FACTOR);

    // iterate ressourcecosts
    $resourcecost = array();
    foreach ($defense->resourceProductionCost as $resourceID => $function) {

      $cost = ceil(eval('return '. formula_parseToPHP($function . ';', '$cave')));
      if ($cost) {
        array_push(
          $resourcecost,
          array(
           'name'        => $GLOBALS['resourceTypeList'][$resourceID]->name,
           'dbFieldName' => $GLOBALS['resourceTypeList'][$resourceID]->dbFieldName,
           'value'       => $cost
          )
        );
      }
    }
    // iterate unitcosts
    $unitcost = array();
    foreach ($defense->unitProductionCost as $unitID => $function){
      $cost = ceil(eval('return '. formula_parseToPHP($function . ';', '$cave')));
      if ($cost)
        array_push($unitcost,
                   array(
                   'name'        => $GLOBALS['unitTypeList'][$unitID]->name,
                   'dbFieldName' => $GLOBALS['unitTypeList'][$unitID]->dbFieldName,
                   'value'       => $cost));
    }

    $buildingCost = array();
    foreach ($defense->buildingProductionCost as $key => $value)
      if ($value != "" && $value != 0)
        array_push($buildingCost, array('dbFieldName' => $GLOBALS['buildingTypeList'][$key]->dbFieldName,
                                        'name'        => $GLOBALS['buildingTypeList'][$key]->name,
                                        'value'       => ceil(eval('return '.formula_parseToPHP($defense->buildingProductionCost[$key] . ';', '$details')))));

    $defenseCost = array();
    foreach ($defense->defenseProductionCost as $key => $value)
      if ($value != "" && $value != 0)
        array_push($defenseCost, array('dbFieldName' => $GLOBALS['defenseSystemTypeList'][$key]->dbFieldName,
                                       'name'        => $GLOBALS['defenseSystemTypeList'][$key]->name,
                                       'value'       => ceil(eval('return '.formula_parseToPHP($defense->defenseProductionCost[$key] . ';', '$details')))));

    $levels[$count] = array('level' => $level + 1,
                            'time'  => $duration,
                            'BUILDINGCOST' => $buildingCost,
                            'DEFENSECOST'  => $defenseCost,
                            'RESOURCECOST' => $resourcecost,
                            'UNITCOST'     => $unitcost);
  }
  if (sizeof($levels))
    $levels = array('population' => $cave['population'], 'LEVEL' => $levels);


  $dependencies     = array();
  $buildingdep      = array();
  $defensesystemdep = array();
  $resourcedep      = array();
  $sciencedep       = array();
  $unitdep          = array();

  foreach ($defense->buildingDepList as $key => $level)
    if ($level)
      array_push($buildingdep, array('name'  => $GLOBALS['buildingTypeList'][$key]->name,
                                     'level' => "&gt;= " . $level));

  foreach ($defense->defenseSystemDepList as $key => $level)
    if ($level)
      array_push($defensesystemdep, array('name'  => $GLOBALS['defenseSystemTypeList'][$key]->name,
                                          'level' => "&gt;= " . $level));

  foreach ($defense->resourceDepList as $key => $level)
    if ($level)
      array_push($resourcedep, array('name'  => $GLOBALS['resourceTypeList'][$key]->name,
                                     'level' => "&gt;= " . $level));

  foreach ($defense->scienceDepList as $key => $level)
    if ($level)
      array_push($sciencedep, array('name'  => $GLOBALS['scienceTypeList'][$key]->name,
                                    'level' => "&gt;= " . $level));

  foreach ($defense->unitDepList as $key => $level)
    if ($level)
      array_push($unitdep, array('name'  => $GLOBALS['unitTypeList'][$key]->name,
                                 'level' => "&gt;= " . $level));


  foreach ($defense->maxBuildingDepList as $key => $level)
    if ($level != -1)
      array_push($buildingdep, array('name'  => $GLOBALS['buildingTypeList'][$key]->name,
                                     'level' => "&lt;= " . $level));

  foreach ($defense->maxDefenseSystemDepList as $key => $level)
    if ($level != -1)
      array_push($defensesystemdep, array('name'  => $GLOBALS['defenseSystemTypeList'][$key]->name,
                                          'level' => "&lt;= " . $level));

  foreach ($defense->maxResourceDepList as $key => $level)
    if ($level != -1)
      array_push($resourcedep, array('name'  => $GLOBALS['resourceTypeList'][$key]->name,
                                     'level' => "&lt;= " . $level));

  foreach ($defense->maxScienceDepList as $key => $level)
    if ($level != -1)
      array_push($sciencedep, array('name'  => $GLOBALS['scienceTypeList'][$key]->name,
                                    'level' => "&lt;= " . $level));

  foreach ($defense->maxUnitDepList as $key => $level)
    if ($level != -1)
      array_push($unitdep, array('name'  => $GLOBALS['unitTypeList'][$key]->name,
                                 'level' => "&lt;= " . $level));


  if (sizeof($buildingdep))
    array_push($dependencies, array('name' => _('Erweiterungen'),
                                    'DEP'  => $buildingdep));

  if (sizeof($defensesystemdep))
    array_push($dependencies, array('name' => _('Verteidigungsanlagen'),
                                    'DEP'  => $defensesystemdep));

  if (sizeof($resourcedep))
    array_push($dependencies, array('name' => _('Rohstoffe'),
                                    'DEP'  => $resourcedep));

  if (sizeof($sciencedep))
    array_push($dependencies, array('name' => _('Forschungen'),
                                    'DEP'  => $sciencedep));

  if (sizeof($unitdep))
    array_push($dependencies, array('name' => _('Einheiten'),
                                    'DEP'  => $unitdep));

  $template->addVars(array(
    'name'          => $defense->name,
    'dbFieldName'   => $defense->dbFieldName,
    'description'   => $defense->description,
    'maxlevel'      => $maxLevel,
    'currentlevel'  => $currentlevel,
    'rangeAttack'   => $defense->attackRange,
    'attackRate'    => $defense->attackRate,
    'defenseRate'   => $defense->defenseRate,
    'size'          => $defense->hitPoints,
    'antiSpyChance' => $defense->antiSpyChance,
    'warPoints'     => $defense->warPoints,
    'levels'        => $levels,
    'depgroup'      => $dependencies,
    'rules_path'    => RULES_PATH)
  );
}

?>
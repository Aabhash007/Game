<?php
/*
 * digest.inc.php -
 * Copyright (c) 2004  OGP Team
 * Copyright (c) 2011-2013 David Unger <unger-dave@gmail.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

require_once('lib/Movement.php');

/*****************************************************************************/
/*                                                                          **/
/*      MOVEMENTS                                                           **/
/*                                                                          **/
/*****************************************************************************/
function digest_getMovements($ownCave, $doNotShow, $showDetails) {
  global $db;

  // get movements
  $ua_movements = Movement::getMovements();

  // caveIDs einsammeln
  $caveIDs = implode(', ', array_map(array($db, 'quote'), array_keys($ownCave)));

  $targetExtraFilds = formula_parseToSelectSQL(GameConstants::EXPOSE_INVISIBLE);
  $sqlExtra = array();
  foreach ($targetExtraFilds as $field) {
    $sqlTargetExtra[] = 'tc.' . $field . ' AS target_' . $field;
  }

  $sql = $db->prepare("SELECT em.*, sc.name AS source_cave_name, sp.name AS source_player_name, st.tag AS source_player_tribe, sc.xCoord AS source_xCoord, sc.yCoord AS source_yCoord, tc.name AS target_cave_name, tp.name AS target_player_name, tt.tag AS target_player_tribe, tc.xCoord AS target_xCoord, tc.yCoord AS target_yCoord " . ((!empty($sqlTargetExtra)) ? ', ' . implode(', ', $sqlTargetExtra) : '') . "
                       FROM " . EVENT_MOVEMENT_TABLE . " em
                         LEFT JOIN " . CAVE_TABLE . " sc ON sc.caveID = em.source_caveID
                         LEFT JOIN " . PLAYER_TABLE . " sp ON sp.playerID = sc.playerID
                         LEFT JOIN " . TRIBE_TABLE . " st ON st.tribeID = sp.tribeID
                         LEFT JOIN " . CAVE_TABLE . " tc ON tc.caveID = em.target_caveID
                         LEFT JOIN " . PLAYER_TABLE . " tp ON tp.playerID = tc.playerID
                         LEFT JOIN " . TRIBE_TABLE . " tt ON tt.tribeID = tp.tribeID
                       WHERE em.source_caveID IN (". $caveIDs .")
                         OR em.target_caveID IN (". $caveIDs .")
                       ORDER BY em.end ASC, em.event_movementID ASC");
  if (!$sql->execute()) return array();

  $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
  $sql->closeCursor();

  // bewegungen durchgehen
  $result = array();
  foreach($rows as $row) {

    // "do not show" movements should not be shown
    if (in_array($row['movementID'], $doNotShow)) continue;

    // is own movement?
    $row['isOwnMovement'] = in_array($row['caveID'], array_keys($ownCave));
    /////////////////////////////////
    // SICHTWEITE BESCHRÄNKEN

/* We got some problems, as reverse movements should not ALWAYS be visible.
 * For example a transport reverse movement should be visible, but a
 * spy reverse movement should not...
 * As a work around we will fix it by not showing any adverse reverse movement.
 *
 * The original code is following...

    if (!$row['isOwnMovement']){

      if ($ua_movements[$row['movementID']]->returnID == -1){
        $sichtweite = getVisionRange($ownCave[$row['source_caveID']]) * $row['speedFactor'];
        $distance = time() - (time_fromDatetime($row['end']) - getDistanceByID($srcID, $destID) * $row['speedFactor']);
      } else {
        $sichtweite = getVisionRange($ownCave[$row['target_caveID']]) * $row['speedFactor'];
        $distance = ceil((time_fromDatetime($row['end']) - time())/60);
      }

      if ($sichtweite < $distance) continue;
    }
 */
    // compute visibility
    if (!$row['isOwnMovement']) {
      // don't show adverse reverse movements
      if ($ua_movements[$row['movementID']]->returnID == -1) continue;

      $sichtweite = getVisionRange($ownCave[$row['target_caveID']]) * $row['speedFactor'];
      $distance = ceil((time_fromDatetime($row['end']) - time())/60);
      if ($sichtweite < $distance) continue;
    }
  /////////////////////////////////


    // ***** fremde unsichtbare bewegung *****
    if ($row['isOwnMovement'] == 0) {
      if ($ua_movements[$row['movementID']]->mayBeInvisible) {
        $anzahl_sichtbarer_einheiten = 0;
        foreach ($GLOBALS['unitTypeList'] as $unitType)
        {
          if ($unitType->visible) {
            $anzahl_sichtbarer_einheiten += $row[$unitType->dbFieldName];
          }
        }

        if ($anzahl_sichtbarer_einheiten == 0) {
          continue;
        }
      }
    }

    $tmp = array(
      'event_id'                => $row['event_movementID'],
      'cave_id'                 => $row['caveID'],
      'source_cave_id'          => $row['source_caveID'],
      'target_cave_id'          => $row['target_caveID'],
      'movement_id'             => $row['movementID'],
      'event_start'             => time_fromDatetime($row['start']),
      'event_start_date'        => time_formatDatetime($row['start']),
      'event_end'               => time_fromDatetime($row['end']),
      'event_end_date'          => time_formatDatetime($row['end']),
      'isOwnMovement'           => intval($row['isOwnMovement']),
      'seconds_before_end'      => time_fromDatetime($row['end']) - time(),
      'movement_id_description' => $ua_movements[$row['movementID']]->description,
      'source_cave_name'        => $row['source_cave_name'],
      'source_player_name'      => $row['source_player_name'],
      'source_player_tribe'     => $row['source_player_tribe'],
      'source_xCoord'           => $row['source_xCoord'],
      'source_yCoord'           => $row['source_yCoord'],
      'target_cave_name'        => $row['target_cave_name'],
      'target_player_name'      => $row['target_player_name'],
      'target_player_tribe'     => $row['target_player_tribe'],
      'target_xCoord'           => $row['target_xCoord'],
      'target_yCoord'           => $row['target_yCoord']
    );

    // ***** Einheiten, Rohstoffe und Artefakte *****
    if ($showDetails) {
      // show artefact
      if ($row['artefactID']) {
        $tmp['artefact'] = artefact_getArtefactByID($row['artefactID']);
      }
      
      // show hero
      if ($row['heroID']) {
        $tmp['hero'] = "Held läuft mit!";
      }

      // eval(GameConstants::EXPOSE_INVISIBLE)
      // FIXME (mlunzena): oben holen wir schon bestimmte Höhlendaten,
      //                   das sollte man zusammenfassen..
      foreach ($targetExtraFilds as $field) {
        $target[$field] = $row['target_' . $field];
      }
      $expose = eval('return '.formula_parseToPHP(GameConstants::EXPOSE_INVISIBLE.";", '$target'));

      // show units
      $units = array();
      foreach ($GLOBALS['unitTypeList'] as $unit) {

        // this movement does not contain units of that type
        if (!$row[$unit->dbFieldName]) continue;

        // expose invisible units
        //   if it is your own move
        //   if unit is visible
        if (!$row['isOwnMovement'] && !$unit->visible) {

          // if target cave's EXPOSEINVISIBLE is > than exposeChance
          if ($expose <= $row['exposeChance']) {
            // do not expose
            continue;
          } else {
            // do something
            // for example:
            // $row[$unit->dbFieldName] *= 2.0 * (double)rand() / (double)getRandMax();
          }
        }

        $units[] = array(
          'unitID'      => $unit->unitID,
          'dbFieldName' => $unit->dbFieldName,
          'name'        => $unit->name,
          'value'       => ($ua_movements[$row['movementID']]->fogUnit && !$row['isOwnMovement']) ? calcFogUnit($row[$unit->dbFieldName]) : $row[$unit->dbFieldName]
        );
      }

      if (sizeof($units)) {
        $tmp['units'] = $units;
      }

      $resources = array();
      foreach ($GLOBALS['resourceTypeList'] as $resource) {
        if (!$row[$resource->dbFieldName]) continue;

        $resources[] = array(
          'name'       => $resource->name,
          'dbFieldName' => $resource->dbFieldName,
          'value'       => ($ua_movements[$row['movementID']]->fogResource && !$row['isOwnMovement']) ? calcFogResource($row[$resource->dbFieldName]) : $row[$resource->dbFieldName]
        );
      }
      if (sizeof($resources)) $tmp['resources'] = $resources;

      if ($row['isOwnMovement'] &&  $ua_movements[$row['movementID']]->returnID != -1 &&  !$row['artefactID'] &&  !$row['blocked']) {
        $tmp['cancel'] = array("modus" => UNIT_MOVEMENT, "eventID" => $row['event_movementID']);
       }
    }

    $result[] = $tmp;
  }

  return $result;
}


/*****************************************************************************/
/*                                                                          **/
/*      INITIATIONS                                                         **/
/*                                                                          **/
/*****************************************************************************/


function digest_getInitiationDates($ownCave) {
  global $db;

  $caveIDs = array();
  foreach ($ownCave as $caveID => $value) {
    array_push($caveIDs, "e.caveID = " . (int) $caveID);
  }
  $caveIDs = implode(" OR ", $caveIDs);

  $sql = $db->prepare("SELECT e.event_artefactID, e.caveID, e.artefactID, e.event_typeID, e.start, e.end, ac.name
                       FROM " . EVENT_ARTEFACT_TABLE . " e
                         LEFT JOIN " . ARTEFACT_TABLE . " a ON e.artefactID = a.artefactID
                         LEFT JOIN " . ARTEFACT_CLASS_TABLE . " ac ON a.artefactClassID = ac.artefactClassID
                         WHERE " . $caveIDs . " ORDER BY e.end ASC, e.event_artefactID ASC");
  if (!$sql->execute()) {
    return array();
  }

  $result = array();
  while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $result[] = array(
      'event_id'           => $row['event_artefactID'],
      'event_typeID'       => $row['event_typeID'],
      'name'               => $ownCave[$row['caveID']]['name'],
      'caveID'             => $row['caveID'],
      'artefactID'         => $row['artefactID'],
      'artefactName'       => $row['name'],
      'event_start'        => time_fromDatetime($row['start']),
      'event_end'          => time_fromDatetime($row['end']),
      'event_end_date'     => time_formatDatetime($row['end']),
      'seconds_before_end' => time_fromDatetime($row['end']) - time()
    );
  }
  $sql->closeCursor();

  return $result;
}


/*****************************************************************************/
/*                                                                          **/
/*      APPOINTMENTS                                                        **/
/*                                                                          **/
/*****************************************************************************/


function digest_getAppointments($ownCaves){
  global $db;

  $caveIDs = implode(', ', array_keys($ownCaves));

  // unit building events
  $result = array();
  $sql = $db->prepare("SELECT *
                       FROM " . EVENT_UNIT_TABLE . "
                       WHERE caveID IN (" . $caveIDs . ")
                       ORDER BY end ASC, event_unitID ASC");
  if ($sql->execute()) {
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $result[] = array(
        'event_name'         => $row['quantity'] . "x " . $GLOBALS['unitTypeList'][$row['unitID']]->name,
        'cave_name'          => $ownCaves[$row['caveID']]['name'],
        'cave_id'            => $row['caveID'],
        'category'           => 'unit',
        'modus'              => UNIT_BUILDER,
        'event_id'           => $row['event_unitID'],
        'event_start'        => time_fromDatetime($row['start']),
        'event_end'          => time_fromDatetime($row['end']),
        'event_end_date'     => time_formatDatetime($row['end']),
        'seconds_before_end' => time_fromDatetime($row['end']) - time());
    }
  }
  $sql->closeCursor();

  // improvement events
  $sql = $db->prepare("SELECT *
                       FROM " . EVENT_EXPANSION_TABLE . "
                       WHERE caveID IN (" . $caveIDs . ")
                       ORDER BY end ASC, event_expansionID ASC");
  $sql->bindValue('caveIDs', $caveIDs);
  if ($sql->execute()) {
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $nextLevel = $ownCaves[$row['caveID']][$GLOBALS['buildingTypeList'][$row['expansionID']]->dbFieldName] + 1;
      $result[] = array(
        'event_name'         => $GLOBALS['buildingTypeList'][$row['expansionID']]->name. " Stufe ". $nextLevel,
        'cave_name'          => $ownCaves[$row['caveID']]['name'],
        'cave_id'            => $row['caveID'],
        'category'           => 'building',
        'modus'              => IMPROVEMENT_BUILDER,
        'event_id'           => $row['event_expansionID'],
        'event_start'        => time_fromDatetime($row['start']),
        'event_end'          => time_fromDatetime($row['end']),
        'event_end_date'     => time_formatDatetime($row['end']),
        'seconds_before_end' => time_fromDatetime($row['end']) - time());
    }
  }
  $sql->closeCursor();

  // defense systms events
  $sql = $db->prepare("SELECT *
                       FROM " . EVENT_DEFENSE_SYSTEM_TABLE . "
                       WHERE caveID IN (" . $caveIDs . ")
                       ORDER BY end ASC, event_defenseSystemID ASC");
  if ($sql->execute()) {
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $nextLevel = $ownCaves[$row['caveID']][$GLOBALS['defenseSystemTypeList'][$row['defenseSystemID']]->dbFieldName] + 1;
      $result[] = array(
        'event_name'         => $GLOBALS['defenseSystemTypeList'][$row['defenseSystemID']]->name . " Stufe ". $nextLevel,
        'cave_name'          => $ownCaves[$row['caveID']]['name'],
        'cave_id'            => $row['caveID'],
        'category'           => 'defense',
        'modus'              => DEFENSE_BUILDER,
        'event_id'           => $row['event_defenseSystemID'],
        'event_start'        => time_fromDatetime($row['start']),
        'event_end'          => time_fromDatetime($row['end']),
        'event_end_date'     => time_formatDatetime($row['end']),
        'seconds_before_end' => time_fromDatetime($row['end']) - time()
      );
    }
  }
  $sql->closeCursor();

  //science events
  $sql = $db->prepare("SELECT *
                       FROM " . EVENT_SCIENCE_TABLE . "
                       WHERE caveID IN (" . $caveIDs . ")
                       ORDER BY end ASC, event_scienceID ASC");
  if ($sql->execute()) {
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $nextLevel = $ownCaves[$row['caveID']][$GLOBALS['scienceTypeList'][$row['scienceID']]->dbFieldName] + 1;
      $result[] = array(
        'event_name'         => $GLOBALS['scienceTypeList'][$row['scienceID']]->name. " Stufe ". $nextLevel,
        'cave_name'          => $ownCaves[$row['caveID']]['name'],
        'cave_id'            => $row['caveID'],
        'category'           => 'science',
        'modus'              => SCIENCE_BUILDER,
        'event_id'           => $row['event_scienceID'],
        'event_start'        => time_fromDatetime($row['start']),
        'event_end'          => time_fromDatetime($row['end']),
        'event_end_date'     => time_formatDatetime($row['end']),
        'seconds_before_end' => time_fromDatetime($row['end']) - time()
      );
    }
  }
  $sql->closeCursor();

  // hero events
  $sql = $db->prepare("SELECT *
                       FROM " . EVENT_HERO_TABLE . "
                       WHERE caveID IN (" . $caveIDs . ")
                       ORDER BY end ASC, event_heroID ASC");
  if ($sql->execute()) {
    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
      $result[] = array(
        'event_name'         => "Wiedererweckung des Helden",
        'cave_name'          => $ownCaves[$row['caveID']]['name'],
        'cave_id'            => $row['caveID'],
        'category'           => 'hero',
        'modus'              => HERO_DETAIL,
        'event_id'           => $row['event_heroID'],
        'event_start'        => time_fromDatetime($row['start']),
        'event_end'          => time_fromDatetime($row['end']),
        'event_end_date'     => time_formatDatetime($row['end']),
        'seconds_before_end' => time_fromDatetime($row['end']) - time()
      );
    }
  }
  $sql->closeCursor();

  usort($result, "datecmp");
  return $result;
}

// for comparing the dates of appointments
function datecmp($a, $b) {
  if ($a['seconds_before_end'] == $b['seconds_before_end'])
    return 0;
  return ($a['seconds_before_end'] < $b['seconds_before_end']) ? -1 : 1;
}

?>
<?php
/*
 * map.inc.php - 
 * Copyright (c) 2004  OGP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

function getCaveDetailsByCoords($minX, $minY, $maxX, $maxY) {
  global $db;

  $caveDetails = array();
  $sql = $db->prepare("SELECT c.terrain, c.name AS cavename, c.caveID, c.xCoord, ".
                       "c.yCoord, c.secureCave, c.artefacts, c.takeoverable, ".
                       "p.name, p.playerID, p.tribe, ".
                       "r.name as region ".
                       "FROM ". CAVE_TABLE ." c LEFT JOIN ". PLAYER_TABLE ." p ".
                       "ON c.playerID = p.playerID ".
                       "LEFT JOIN ". REGIONS_TABLE ." r ".
                       "ON c.regionID = r.regionID ".
                       "WHERE :minX <= c.xCoord AND c.xCoord <= :maxX ".
                       "AND   :minY <= c.yCoord AND c.yCoord <= :maxY ".
                       "ORDER BY c.yCoord, c.xCoord");
  $sql->bindValue('minX', $minX, PDO::PARAM_INT);
  $sql->bindValue('minY', $minY, PDO::PARAM_INT);
  $sql->bindValue('maxX', $maxX, PDO::PARAM_INT);
  $sql->bindValue('maxY', $maxY, PDO::PARAM_INT);
  
  if($sql->execute())
    while($row = $sql->fetch(PDO::FETCH_ASSOC))
      array_push($caveDetails, $row);
  return $caveDetails;
}

function getEmptyCell(){
  return array('EMPTY' => array('iterate' => ''));
}

function getCornerCell() {
  return array('CORNER' => array('iterate' => ''));
}

function getLegendCell($name, $value){
  return array('HEADER' => array('text' => "<small>$name: $value</small>"));
}

function getMapCell($map, $xCoord, $yCoord){
  if (!is_array($map[$xCoord][$yCoord]))
    return getEmptyCell();
  else
    return array('MAPCELL' => $map[$xCoord][$yCoord]);
}

?>
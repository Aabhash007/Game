<?php
/*
 * hero.inc.php - basic hero system
 * Copyright (c) 2003  OGP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once("game_rules.php");
require_once("hero.rules.php");
//init Potions
init_potions();
//init HeroTypes
init_heroTypes();
//init HeroSkills
init_heroSkills();


function getHeroByPlayer($playerID) {
  global $db, $heroTypesList;

  // set database query with playerID
  $sql = $db->prepare("SELECT *
                       FROM ". HERO_TABLE ." 
                         WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID);

  // if not successful
  if (!$sql->execute() || !($result = $sql->fetch(PDO::FETCH_ASSOC))) {
    $sql->closeCursor();
    return null;
  }
  //otherwise

  if (empty($result))
    return null;

  // otherwise
  $sql->closeCursor();
  $hero = array(
      'heroID'       => $result['heroID'],
      'playerID'     => $result['playerID'],
      'name'         => $result['name'],
      'heroTypeID'   => $result['heroTypeID'],
      'lvl'          => $result['lvl'],
      'exp'          => $result['exp'],
      'caveID'       => $result['caveID'],
      'isAlive'      => $result['isAlive'],
      'tpFree'       => $result['tpFree'],
      'healPoints'           => $result['healPoints'],
      'maxHealPoints'        => $result['maxHealPoints'],
      'forceLvl'     => $result['forceLvl'],
      'maxHpLvl'     => $result['maxHpLvl'],
      'regHpLvl'     => $result['regHpLvl'],
      'path'         => _('hero_imperator.gif'),
      'location'     => _('tot')
  );
  if($hero['id']=='Defender'){
    $hero['path']=_('hero_defender.gif');
  }
  if($hero['id']=='Constructor'){
    $hero['path']=_('hero_constructor.gif');
  }
  
  $hero['force'] = eval("return " . hero_parseFormulas($heroTypesList[$hero['heroTypeID']]['force']) . ";");
  $hero['lvlUp'] = eval("return " . hero_parseFormulas($heroTypesList[$hero['heroTypeID']]['lvl']) . ";");
  $hero['expLeft']= $hero['lvlUp'] - $hero['exp'];
  $hero['regHealPoints'] = eval("return " . hero_parseFormulas($heroTypesList[$hero['heroTypeID']]['regHP']) . ";");
  
  if ($hero['healPoints'] == 0 || $hero['isAlive'] == false) {
      $hero['location'] = _('wird wiederbelebt');
      if ($eventHero == true) {
        $disabled = _('');
        $hero['location'] = _('tot');
      }
      $hero['path'] = _('hero_death.gif');
    }
    elseif($hero['caveID'] == 0) {
      $hero['location'] = _('in Bewegung');
    }
    else {
      $cave = getCaveByID($hero['caveID']);
      $hero['location'] = $cave['name'] . " in (" . $cave['xCoord'] . "|" . $cave['yCoord'] .")";
    }
  
  return $hero;

}

function hero_parseFormulas ($formula) {
  $formula = str_replace(
    array(
      '{forceLvl}',
      '{lvl}', 
      '{exp}',
      '{regHpLvl}',
      '{healPoints}', 
      '{maxHealPoints}',
      '{tpFree}', 
      '{maxHpLvl}'
    ), 
    array(
      '$hero[\'forceLvl\']',
      '$hero[\'lvl\']',
      '$hero[\'exp\']',
      '$hero[\'regHpLvl\']',
      '$hero[\'healPoints\']',
      '$hero[\'maxHealPoints\']',
      '$hero[\'tpFree\']',
      '$hero[\'maxHpLvl\']'
    ), $formula);
  
  return $formula;
}

function getEventHero($playerID) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("SELECT *
                       FROM ". EVENT_HERO_TABLE ." 
                         WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID);

  // if not successful
  if (!$sql->execute() || !$sql->fetch(PDO::FETCH_ASSOC)) {
    $sql->closeCursor();
    return true;
  }
  // otherwise
  $sql->closeCursor();
  return false;

}
function getHeroQueue($playerID) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("SELECT *
                       FROM ". EVENT_HERO_TABLE ." 
                         WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID);

  // if not successful
  if (!$sql->execute() || !($result=$sql->fetch(PDO::FETCH_ASSOC))){
    $sql->closeCursor();
    return null;
  }
  // otherwise
  $sql->closeCursor();
  return $result;

}
function skillForce($playerID) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("UPDATE ". HERO_TABLE ."
                       SET forceLvl = forceLvl + 1,
                         tpFree = tpFree - 1
                       WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
  
  
  return $sql->execute();
}
function skillMaxHp($playerID,$maxHP) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("UPDATE ". HERO_TABLE ."
                       SET maxHpLvl = maxHpLvl + 1,
             tpFree = tpFree - 1,
             maxHealPoints = :maxHP
                         WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
  $sql->bindValue('maxHP', $maxHP, PDO::PARAM_INT);
  return $sql->execute();
}
function skillRegHp($playerID) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("UPDATE ". HERO_TABLE ."
                       SET regHpLvl = regHpLvl + 1,
             tpFree = tpFree - 1
                         WHERE playerID = :playerID");
  $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
  return $sql->execute();

}
function getRitualByLvl($lvl) {
  global $db;

  // set database query with playerID
  $sql = $db->prepare("SELECT *
                       FROM ". HERO_RITUAL_TABLE ." 
                         WHERE ritualID  = :lvl");
  $sql->bindValue('lvl', $lvl, PDO::PARAM_INT);

  // if not successful
  if (!$sql->execute() || !($ritual=$sql->fetch(PDO::FETCH_ASSOC))){
    $sql->closeCursor();
    return null;
  }
  // otherwise
  $sql->closeCursor();
  return $ritual;

}

function createRitual($caveID, $playerID, $ritual, $hero, &$ownCaves) {
  global $db;

  $cave = getCaveSecure($caveID, $playerID);

  if ($ritual['population']['value']<= $cave['population'] &&
  $ritual['food']['value']<= $cave['food'] &&
  $ritual['wood']['value']<= $cave['wood'] &&
  $ritual['stone']['value']<= $cave['stone'] &&
  $ritual['metal']['value']<= $cave['metal'] &&
  $ritual['sulfur']['value']<= $cave['sulfur'])
  {

    $sql = $db->prepare("UPDATE ". CAVE_TABLE ."
                       SET population = population - :pop,
             food = food - :food,
             wood = wood - :wood,
             stone = stone - :stone,
             metal = metal - :metal,
             sulfur = sulfur - :sulfur
                         WHERE (playerID = :playerID) AND (caveID = :caveID)");
    $sql->bindValue('pop', $ritual['population']['value'], PDO::PARAM_INT);
    $sql->bindValue('food', $ritual['food']['value'], PDO::PARAM_INT);
    $sql->bindValue('wood', $ritual['wood']['value'], PDO::PARAM_INT);
    $sql->bindValue('stone', $ritual['stone']['value'], PDO::PARAM_INT);
    $sql->bindValue('metal', $ritual['metal']['value'], PDO::PARAM_INT);
    $sql->bindValue('sulfur', $ritual['sulfur']['value'], PDO::PARAM_INT);
    $sql->bindValue('caveID', $caveID, PDO::PARAM_INT);
    $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);

    if (!$sql->execute()) {
      $sql->closeCursor();
      return -7;
    }
    $now = time();
    $sql = $db->prepare("INSERT INTO ". EVENT_HERO_TABLE ." (caveID, playerID, heroID,
                        start, end, blocked) 
                     VALUES (:caveID, :playerID, :heroID, :start, :end, :blocked)");      
    $sql->bindValue('caveID', $caveID, PDO::PARAM_INT);
    $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
    $sql->bindValue('heroID', $hero['heroID'], PDO::PARAM_INT);
    $sql->bindValue('start', time_toDatetime($now), PDO::PARAM_STR);
    $sql->bindValue('end', time_toDatetime($now + $ritual['duration']), PDO::PARAM_STR);
    $sql->bindValue('blocked', 0, PDO::PARAM_INT);
    if ($sql->execute()) {
      $sql->closeCursor();

      $sql =  $db->prepare("UPDATE " . HERO_TABLE . " SET
                isAlive = -1, caveID = :caveID WHERE heroID = :heroID");
      $sql->bindValue('heroID', $hero['heroID'], PDO::PARAM_INT);
      $sql->bindValue('caveID', $caveID, PDO::PARAM_INT);

      if (!$sql->execute()) {
        return -3;
      }
      
      // update cave
      $ownCaves[$caveID] = $ownCaves[$caveID] = getCaveSecure($caveID, $_SESSION['player']->playerID);
      
      return 2;
    }
  }
  return -3;
}
function createNewHero($heroTypeID, $playerID, $caveID) {
  global $db, $heroTypesList;

  $hero = getHeroByPlayer($playerID);

  if($hero == null) {
    $player = getPlayerByID($playerID);

    $sql = $db->prepare("INSERT INTO ". HERO_TABLE ."
                    (caveID, playerID, heroTypeID, name, exp,
             healPoints, maxHealPoints, isAlive) 
                     VALUES (
             :caveID, :playerID, :heroTypeID, :name, :exp, 
             :healPoints, :maxHealPoints, :isAlive)");
    $sql->bindValue('caveID', $caveID, PDO::PARAM_INT);
    $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
    $sql->bindValue('heroTypeID', $heroTypeID, PDO::PARAM_INT);
    $sql->bindValue('name', $player['name'], PDO::PARAM_INT);
    $sql->bindValue('exp', 100, PDO::PARAM_INT);
    $sql->bindValue('healPoints', 0, PDO::PARAM_INT);
    $sql->bindValue('maxHealPoints', 100, PDO::PARAM_INT);
    $sql->bindValue('isAlive', 0, PDO::PARAM_INT);

    if (!$sql->execute()) {
      $sql->closeCursor();
      echo $sql->errorInfo();
      return -6;
    }

    // effects
    foreach ($heroTypesList[$heroTypeID]['effects'] AS $key => $value) {
      $sql = $db->prepare("UPDATE " .HERO_TABLE . "
                         SET " . $key ." = :value");
      $sql->bindValue('value', $value['absolute'], PDO::PARAM_STR);
      
      if (!$sql->execute()) {
        $sql->closeCursor();
        echo $sql->errorInfo();
        return -6;
      }
    }
    
    return 3;
  }
  return -6;
}

function hero_removeHeroFromCave ($heroID) {
  global $db;

  $sql = $db->prepare("UPDATE " . CAVE_TABLE ." SET
             hero = 0
             WHERE hero = :heroID");
  $sql->bindValue('heroID', $heroID, PDO::PARAM_INT);

  if (!$sql->execute())
    return false;

  $sql = $db->prepare("UPDATE " . HERO_TABLE . " SET
                       isMoving = 1 
                       WHERE heroID = :heroID");
  $sql->bindValue('heroID', $heroID, PDO::PARAM_INT);
  
  if (!$sql->execute())
    return false;
    
  return true;
}

function hero_usePotion ($potionID, $value) {
  global $db, $potionTypeList;
  
  $playerID = $_SESSION['player']->playerID;
  
  $playerData = getPlayerByID($playerID);
  $hero = getHeroByPlayer($playerID);
  
  $potion = $potionTypeList[$potionID];
  
  if (!$potion)
    return -8;
  
  if ($potion->needed_level > $hero['lvl'])
    return -10;
    
  if ($playerData[$potion->dbFieldName] < $value)
    return -9; 
  
  // remove potions
  $sql = $db->prepare("UPDATE " . PLAYER_TABLE ."
                       SET " . $potion->dbFieldName . " = " . $potion->dbFieldName . " - :value
                       WHERE playerID = :playerID");
  $sql->bindValue('value', $value, PDO::PARAM_INT);
  $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
  
  $sql_setback = $db->prepare("UPDATE " . PLAYER_TABLE ."
                       SET " . $potion->dbFieldName . " = " . $potion->dbFieldName . " + :value
                       WHERE playerID = :playerID");
  $sql_setback->bindValue('value', $value, PDO::PARAM_INT);
  $sql_setback->bindValue('playerID', $playerID, PDO::PARAM_INT);
  
  if (!$sql->execute()) {
    return -8;
  }
    
  
  // apply potion effects
  $newHealPoints = $hero['HP'];
  for ($i = 0; $i< $value; $i ++) {
    $newHealPoints += floor($hero['maxHP'] * $potion->hp_prozentual_increase/100) + 
                   $potion->hp_increase;
  }
  if ($hero['maxHealPoints'] < $newHealPoints)
    $newHealPoints = $hero['maxHealPoints'];
  var_dump($hero);
  if ($potion->tp_setBack == false) {
    $sql = $db->prepare("UPDATE " .HERO_TABLE ."
                         SET healPoints = :newHealPoints
                         WHERE playerID = :playerID");
    $sql->bindValue('newHealPoints', $newHealPoints, PDO::PARAM_INT);
    $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
    
    if (!$sql->execute()) {
      $sql_setback->execute();
      return -8;
    }
  } else {
    $sql = $db->prepare("UPDATE " . HERO_TABLE ."
                         SET maxHpLvl = 0, forceLvl = 0, regHpLvl = 0
                         WHERE playerID = :playerID");
    $sql->bindValue('playerID', $playerID, PDO::PARAM_INT);
    
    if (!$sql->execute()) {
      $sql_setback->execute();
      return -8;
    }
    return 6;
  }

  return 5;
}


?>
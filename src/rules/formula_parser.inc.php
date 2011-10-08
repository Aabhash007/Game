<?
global $cfg;
require_once($cfg['cfgpath'] . "game_rules.php");
require_once($cfg['cfgpath'] . "effect_list.php");

init_Buildings();
init_Units();
init_Resources();
init_Sciences();
init_DefenseSystems();
init_Effects();

function sign($value){
  if ($value > 0) return 1;
  if ($value < 0) return -1;
  return 0;
}

$FORMULA_SYMBOLS = array("R" => $resourceTypeList,
                         "B" => $buildingTypeList,
                         "U" => $unitTypeList,
                         "S" => $scienceTypeList,
                         "D" => $defenseSystemTypeList,
                         "E" => $effectTypeList);

$FORMULA_READABLE = array("LEAST"    => "Min",
                          "GREATEST" => "Max",
                          "POW"      => "Potenz");

$FORMULA_PHP_FUNCTIONS = array("LEAST"    => "min",
                               "GREATEST" => "max");

function formula_parseToReadable($formula){
  global $FORMULA_SYMBOLS, $FORMULA_READABLE;

  foreach($FORMULA_READABLE as $key => $value)
    $formula = str_replace($key, $value, $formula);

  $sql = '';
  // parse symbols
  for ($i = 0; $i < strlen($formula); $i++){

    // opening brace
    if ($formula{$i} == '['){

      $symbol = $formula{++$i};
      $index = 0;

      while($formula{++$i} != '.')
        $index = $index * 10 + ($formula{$i} + 0);

      $field  = substr($formula, ++$i, 3);

      // 'ACT]' or 'MAX]'
      $i += 3;

      if (strncasecmp($field, "ACT", 3) == 0)
        $sql .= $FORMULA_SYMBOLS[$symbol][$index]->name;



      else if (strncasecmp($field, "MAX", 3) == 0)
        $sql .= formula_parseToReadable($FORMULA_SYMBOLS[$symbol][$index]->maxLevel);

    } else {
      $sql .= $formula{$i};
    }
  }

  return $sql;

}

function formula_parseBasic($formula){
  global $FORMULA_SYMBOLS, $FORMULA_PHP_FUNCTIONS;

  foreach($FORMULA_PHP_FUNCTIONS as $key => $value) {
    $formula = str_replace($key, $value, $formula);
  }

  $sql = '';
  // parse symbols
  for ($i = 0; $i < strlen($formula); $i++) {
    // opening brace
    if ($formula{$i} == '[') {

      $symbol = $formula{++$i};
      $index = 0;

      while($formula{++$i} != '.') {
        $index = $index * 10 + ($formula{$i} + 0);
      }

      $field  = substr($formula, ++$i, 3);

      // 'ACT]' or 'MAX]'
      $i += 3;
      if (strncasecmp($field, "ACT", 3) == 0) {
        $sql .= "0";
      } else if (strncasecmp($field, "MAX", 3) == 0) {
        $sql .= formula_parseBasic($FORMULA_SYMBOLS[$symbol][$index]->maxLevel);
      }
    } else {
      $sql .= $formula{$i};
    }
  }

  return $sql;
}

function rules_checkDependencies($object){

  global $buildingTypeList,
         $defenseSystemTypeList,
         $resourceTypeList,
         $scienceTypeList,
         $unitTypeList,
         $effectTypeList;

  $dependencies = array();

  if (is_array($object->buildingDepList))
    foreach($object->buildingDepList as $key => $value)
      if ($value != "" && $value != "0")
        array_push($dependencies, array('name'     => $buildingTypeList[$key]->name,
                                        'category' => "buildings",
                                        'id'       => $buildingTypeList[$key]->buildingID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxBuildingDepList))
    foreach($object->maxBuildingDepList as $key => $value)
      if ($value != -1)
        array_push($dependencies, array('name'     => $buildingTypeList[$key]->name,
                                        'category' => "buildings",
                                        'id'       => $buildingTypeList[$key]->buildingID,
                                        'level'    => "&lt;= " . $value));

  if (is_array($object->scienceDepList))
    foreach($object->scienceDepList as $key => $value)
      if ($value != "" && $value != "0")
        array_push($dependencies, array('name'     => $scienceTypeList[$key]->name,
                                        'category' => "sciences",
                                        'id'       => $scienceTypeList[$key]->scienceID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxScienceDepList))
    foreach($object->maxScienceDepList as $key => $value)
      if ($value != -1)
        array_push($dependencies, array('name'     => $scienceTypeList[$key]->name,
                                        'category' => "sciences",
                                        'id'       => $scienceTypeList[$key]->scienceID,
                                        'level'    => "&lt;= " . $value));

  if (is_array($object->defenseSystemDepList))
    foreach($object->defenseSystemDepList as $key => $value)
      if ($value != "" && $value != "0")
        array_push($dependencies, array('name'     => $defenseSystemTypeList[$key]->name,
                                        'category' => "defenses",
                                        'id'       => $defenseSystemTypeList[$key]->defenseSystemID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxDefenseSystemDepList))
    foreach($object->maxDefenseSystemDepList as $key => $value)
      if ($value != -1)
        array_push($dependencies, array('name'     => $defenseSystemTypeList[$key]->name,
                                        'category' => "defenses",
                                        'id'       => $defenseSystemTypeList[$key]->defenseSystemID,
                                        'level'    => "&lt;= " . $value));

  if (is_array($object->resourceDepList))
    foreach($object->resourceDepList as $key => $value)
      if ($value != "" && $value != "0")
        array_push($dependencies, array('name'     => $resourceTypeList[$key]->name,
                                        'category' => "resources",
                                        'id'       => $resourceTypeList[$key]->resourceID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxResourceDepList))
    foreach($object->maxResourceDepList as $key => $value)
      if ($value != -1)
        array_push($dependencies, array('name'     => $resourceTypeList[$key]->name,
                                        'category' => "resources",
                                        'id'       => $resourceTypeList[$key]->resourceID,
                                        'level'    => "&lt;= " . $value));

  if (is_array($object->unitDepList))
    foreach($object->unitDepList as $key => $value)
      if ($value != "" && $value != "0")
        array_push($dependencies, array('name'     => $unitTypeList[$key]->name,
                                        'category' => "units",
                                        'id'       => $unitTypeList[$key]->unitID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxUnitDepList))
    foreach($object->maxUnitDepList as $key => $value)
      if ($value != -1)
        array_push($dependencies, array('name'     => $unitTypeList[$key]->name,
                                        'category' => "units",
                                        'id'       => $unitTypeList[$key]->unitID,
                                        'level'    => "&lt;= " . $value));

  if (is_array($object->effectDepList))
    foreach($object->effectDepList as $key => $value)
      if (!is_null($value))
        array_push($dependencies, array('name'     => $effectTypeList[$key]->name,
                                        'category' => "effects",
                                        'id'       => $effectTypeList[$key]->effectID,
                                        'level'    => "&gt;= " . $value));
  if (is_array($object->maxEffectDepList))
    foreach($object->maxEffectDepList as $key => $value)
      if (!is_null($value))
        array_push($dependencies, array('name'     => $effectTypeList[$key]->name,
                                        'category' => "effects",
                                        'id'       => $effectTypeList[$key]->effectID,
                                        'level'    => "&lt= " . $value));
  return $dependencies;
}

?>

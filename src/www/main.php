<?php
/*
 * main.php -
 * Copyright (c) 2003  OGP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** Set flag that this is a parent file */
define("_VALID_UA", 1);

require_once("config.inc.php");

require_once("include/page.inc.php");
require_once("include/template.inc.php");
require_once("include/db.functions.php");
require_once("include/time.inc.php");
require_once("include/basic.lib.php");
require_once("include/vote.html.php");
require_once("include/wonder.rules.php");
require_once("modules/Messages/Messages.php");

page_start();

// session expired?
if (page_sessionExpired())
  page_error403("Sie waren f�r " . ((int)(SESSION_MAX_LIFETIME/60)) . " Minuten oder mehr inaktiv. Letzte Aktion um " . date("H:i:s", $_SESSION['lastAction'] . " Uhr."));
else
  $_SESSION['lastAction'] = time();

// session valid?
if (!page_sessionValidate())
  page_error403("Deine Session ist ung&uuml;ltig.");

// refresh user data
page_refreshUserData();

// load template
$template = new Template;

// get modus
$modus = page_getModus();

// get caves
$ownCaves = getCaves($_SESSION['player']->playerID);

// no caves left
if (!$ownCaves) {
  if (!in_array($modus, $config->noCaveModusInclude))
    $modus = NO_CAVE_LEFT;
} else {
  // caveID is not sent
  if (!$caveID = request_var('caveID', 0)) {
    if (!isset($_SESSION['caveID'])) {
      $temp = current($ownCaves);
      $caveID = $temp['caveID'];
    } else {
      $caveID = $_SESSION['caveID'];
    }
  }
  $_SESSION['caveID'] = $caveID;

  // my cave?
  if (!array_key_exists($caveID, $ownCaves)) {
    $modus = NOT_MY_CAVE;
    $_SESSION['caveID'] = NULL;
  }
}

// include required files
if (is_array($require_files[$modus])) {
  foreach($require_files[$modus] as $file) {
    require_once('include/' . $file);
  }
}

// log request
page_logRequest($modus, $caveID);

// log ore
page_ore();

################################################################################


///////////////////////////////////////////////////////////////////////////////
$showads = false;
switch ($modus) {

  /////////////////////////////////////////////////////////////////////////////
  // UEBERSICHTEN                                                            //
  /////////////////////////////////////////////////////////////////////////////

  case NO_CAVE_LEFT:
    $pagetitle = _("Keine H&ouml;hle mehr");
    $content = _("Leider besitzen sie keine H&ouml;hle mehr.");
    break;

  case NOT_MY_CAVE:
    $pagetitle = _("Fehler");
    $content = _("Diese H&ouml;hle geh&ouml;rt nicht ihnen.");
    break;

  case CAVE_DETAIL:
    $pagetitle = _("H&ouml;hlendetails");
    $content = getCaveDetailsContent($ownCaves[$caveID]);
    break;

  case ALL_CAVE_DETAIL:
    $pagetitle = _("H&ouml;hlen-&Uuml;bersicht");
    $content = getAllCavesDetailsContent($ownCaves);
    break;

  case CAVE_GIVE_UP_CONFIRM:
    $pagetitle = _("H&ouml;hle aufgeben");
    $giveUpCaveID = request_var('giveUpCaveID', 0);
    if (isset($ownCaves[$giveUpCaveID]))
      $content = cave_giveUpConfirm($ownCaves[$giveUpCaveID]);
    else
      $content = "Die H&ouml;le befindet sch nicht in deinem Besitz.";
    break;

  case END_PROTECTION_CONFIRM:
    $pagetitle = _("Anf&auml;ngerschutz beenden");
    $content = beginner_endProtectionConfirm($ownCaves[$caveID]);
    break;

  case EASY_DIGEST:
    $pagetitle = _("Termin-&Uuml;bersicht | Runde Tetraktys");
    $content = digest_getDigest($ownCaves);
    break;

  case EVENT_REPORTS:
    list($pagetitle, $content) = eventReports_main($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // ARTEFAKTE                                                               //
  /////////////////////////////////////////////////////////////////////////////

  case ARTEFACT_DETAIL:
    $pagetitle = _("Artefaktdetail");
    $content = artefact_getDetail($caveID, $ownCaves);
    break;
  case ARTEFACT_LIST:
    $pagetitle = _("Artefakte");
    $content = artefact_getList($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // NACHRICHTEN                                                             //
  /////////////////////////////////////////////////////////////////////////////

  case MESSAGES_LIST:
    $deletebox = request_var('deletebox', array('' => ''));
    $box = request_var('box', 1);

    $pagetitle = _("Nachrichten");
    $content = messages_getMessages($caveID, $deletebox, $box);
    break;

  case MESSAGE_READ:
    $messageID = request_var('messageID', 0);
    $box = request_var('box', 1);

    $pagetitle = _("Nachricht lesen");
    $content = messages_showMessage($caveID, $messageID, $box);
    break;

  case MESSAGE_NEW:
    $pagetitle = _("Nachricht schreiben");
    $content = messages_newMessage($caveID);
    break;

  case NEW_MESSAGE_RESPONSE:
    $pagetitle = _("Verschicken einer Nachricht");
    $content = messages_sendMessage($caveID);
    break;

  case CONTACTS:
    list($pagetitle, $content) = contacts_main($caveID, $ownCaves);
    break;

  case CAVE_BOOKMARKS:
    list($pagetitle, $content) = cavebookmarks_main($caveID, $ownCaves);
    break;

  case DONATIONS:
    list($pagetitle, $content) = donations_main($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // KARTEN                                                                  //
  /////////////////////////////////////////////////////////////////////////////

  case MAP:
    $pagetitle = _("H&ouml;hlenkarte");
    $content = getCaveMapContent($caveID, $ownCaves);
    break;

  case MAP_DETAIL:
    $pagetitle = _("H&ouml;hlenbericht");
    $content = getCaveReport($caveID, $ownCaves, request_var('targetCaveID', 0));
    break;

  /////////////////////////////////////////////////////////////////////////////
  // ERWEITERUNGEN                                                           //
  /////////////////////////////////////////////////////////////////////////////

  case IMPROVEMENT_BUILDER:
    $pagetitle = _("Erweiterungen errichten");
    $content = improvement_getImprovementDetail($caveID, $ownCaves[$caveID]);
    break;

  case IMPROVEMENT_DETAIL:
    $pagetitle = _("Geb&auml;udeerweiterungen");
    $content = improvement_getBuildingDetails(request_var('buildingID', 0), $ownCaves[$caveID]);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // WONDERS                                                                 //
  /////////////////////////////////////////////////////////////////////////////

  case WONDER:
    $pagetitle = _("Wunder erwirken");
    $content = wonder_getWonderContent($caveID, $ownCaves[$caveID]);
    break;

  case WONDER_DETAIL:
    $pagetitle = _("Wunder");
    $content = wonder_getWonderDetailContent(request_var('wonderID', 0), $ownCaves[$caveID]);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // VERTEIDIGUNGSANLAGEN                                                    //
  /////////////////////////////////////////////////////////////////////////////

  case DEFENSE_BUILDER:
    $pagetitle = _("Verteidigungsanlagen und externe Geb&auml;ude errichten");
    $content = defense_builder($caveID, $ownCaves[$caveID]);
    break;

  case DEFENSE_DETAIL:
    $pagetitle = _("Verteidigungsanlagen und externe Geb&auml;ude");
    $content = defense_showProperties($ownCaves[$caveID]);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // WISSENSCHAFT                                                            //
  /////////////////////////////////////////////////////////////////////////////

  case SCIENCE_BUILDER:
    $pagetitle = _("Wissen erforschen");
    $content = science_getScienceDetail($caveID, $ownCaves[$caveID]);
    break;

  case SCIENCE_DETAIL:
    $pagetitle = _("Forschungen");
    $content = science_getScienceDetails(request_var('scienceID', 0), $ownCaves[$caveID]);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // EINHEITEN                                                               //
  /////////////////////////////////////////////////////////////////////////////

  case UNIT_BUILDER:
    $pagetitle = _("Einheiten bauen");
    $content = unit_getUnitDetail($caveID, $ownCaves[$caveID]);
    break;


  case UNIT_DETAIL:
    $pagetitle = _("Einheitsattribute");
    $content = unit_showUnitProperties(request_var('unitID', 0), $ownCaves[$caveID]);
    break;

  case UNIT_MOVEMENT:
    $pagetitle = _("Einheiten bewegen");
    $content = unit_Movement($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // MISSIONIEREN                                                            //
  /////////////////////////////////////////////////////////////////////////////

  case TAKEOVER:
    $pagetitle = _("Missionieren");
    $content = takeover_main($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // PUNKTZAHLEN                                                             //
  /////////////////////////////////////////////////////////////////////////////

  case RANKING_PLAYER:
    $pagetitle = _("Spielerranking");
    $offset  = ranking_checkOffset($_SESSION['player']->playerID, request_var('offset', 0));
    $content = ranking_getContent($caveID, $offset);
    break;

  case RANKING_TRIBE:
    $pagetitle = _("Stammesranking");
    $offset  = rankingTribe_checkOffset(request_var('offset', 0));
    $content = rankingTribe_getContent($caveID, $offset);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // TRIBES                                                                  //
  /////////////////////////////////////////////////////////////////////////////

  case TRIBE:
    $pagetitle = _("St&auml;mme");
    $content = tribe_getContent($_SESSION['player']->playerID, $_SESSION['player']->tribe);
    break;

  case TRIBE_ADMIN:
    $pagetitle = _("Stamm verwalten");
    $content = tribeAdmin_getContent($_SESSION['player']->playerID, $_SESSION['player']->tribe);
    break;

  case TRIBE_RELATION_LIST:
    $pagetitle = _("Beziehungen");
    $content = tribeRelationList_getContent(request_var('tag', ''));
    break;

  case TRIBE_HISTORY:
    $pagetitle = _("Stammesgeschichte");
    $content = tribeHistory_getContent(request_var('tag', ''));
    break;

  case TRIBE_DELETE:
    $pagetitle = _("Stamm verwalten");
    $confirm = isset($_POST['confirm']) ? true : false;
    $content = tribeDelete_getContent($_SESSION['player']->playerID, $_SESSION['player']->tribe, $confirm);
    break;

  case TRIBE_LEADER_DETERMINATION:
    $pagetitle = _("Stammesanf&uuml;hrer bestimmen");
    $content = tribeLeaderDetermination_getContent($_SESSION['player']->playerID, $_SESSION['player']->tribe);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // FRAGEB�GEN                                                              //
  /////////////////////////////////////////////////////////////////////////////

  case QUESTIONNAIRE:
    $pagetitle = _("Fragebogen");
    $content = questionnaire_getQuestionnaire($caveID, $ownCaves);
    break;

  case QUESTIONNAIRE_PRESENTS:
    $pagetitle = _("Fragebogen Treuebonus");
    $content = questionnaire_presents($caveID, $ownCaves);
    break;

  case SUGGESTIONS:
    list($pagetitle, $content) = suggestions_main($caveID, $ownCaves);
    break;

  /////////////////////////////////////////////////////////////////////////////
  // HELDEN                                                                  //
  /////////////////////////////////////////////////////////////////////////////
/*
  case HERO_DETAIL:
    $pagetitle = _("Mein Held");
    $content = hero_getHeroDetail($caveID, $ownCaves);
    break;
*/
  /////////////////////////////////////////////////////////////////////////////
  // AWARDS                                                                  //
  /////////////////////////////////////////////////////////////////////////////

  case AWARD_DETAIL:
    $pagetitle = _("Auszeichnung");
    $content = award_getAwardDetail(request_var('award', 0));
    break;

  /////////////////////////////////////////////////////////////////////////////
  // DIVERSES                                                                //
  /////////////////////////////////////////////////////////////////////////////

  case NEWS:
    $pagetitle = _("Uga-Agga News");
    $content = rssFeedNews_getContent();
    break;

  case STATISTIC:
    $pagetitle = _("Uga-Agga Statistik");
    $content = statistic_getContent();
    break;

  case EFFECTWONDER_DETAIL:
    $pagetitle = _("Aktive Effekte und Wunder");
    $content = effect_getEffectWonderDetailContent($caveID, $ownCaves[$caveID]);
    break;

  case WEATHER_REPORT:
    $pagetitle = _('Wetterbericht');
    $content = weather_getReport();
    break;

  case USER_PROFILE:
    $pagetitle = _("Einstellungen");
    $content = profile_main();
    break;

  case DELETE_ACCOUNT:
    $pagetitle = _("Account l&ouml;schen");
    $content = profile_deleteAccount($_SESSION['player']->playerID);
    break;

  case PLAYER_DETAIL:
    $pagetitle = _("Spielerbeschreibung");
    $content = player_getContent($caveID, request_var('detailID', 0));
    break;

  case TRIBE_DETAIL:
    $pagetitle = _("Stammesbeschreibung");
    $content = tribe_getContent($caveID, request_var('tribe', ""));
    break;

  case TRIBE_PLAYER_LIST:
    $pagetitle = _("Stammesmitglieder ...");
    $content = tribePlayerList_getContent($caveID, request_var('tag', ''));
    break;

  case DYK:
    $pagetitle = _("Infos rund um Uga-Agga");
    $content = doYouKnow_getContent();
  break;

  case MERCHANT:
    $pagetitle = _("Der H&auml;ndler");
    $content = merchant_getMechantDetail($_SESSION['player']->playerID, $caveID, $ownCaves[$caveID]);
  break;

  case MAIL:
    list($pagetitle, $content) = suggestions_main($caveID, $ownCaves);
  break;


  case LOGOUT:
    session_destroy();
    Header("Location: logout.php");
    break;

  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////

  default:
    $pagetitle = _("Modus nicht bekannt");
    $content = "Modus " . $modus . "ist nicht verf&uuml;gbar. CaveID :" . $caveID;
}

// prepare resource bar
$resources = array();
if (!isset($no_resource_flag)) $no_resource_flag = false;
if (!$no_resource_flag && isset($resourceTypeList)) {
  foreach ($resourceTypeList as $resource) {
    $amount = floor($ownCaves[$caveID][$resource->dbFieldName]);
    if (!$resource->nodocumentation || $amount > 0) {
      $delta = $ownCaves[$caveID][$resource->dbFieldName . "_delta"];
      if ($delta > 0) $delta = "+" . $delta;
      $resources['resources'][] = array(
        'dbFieldName'  => $resource->dbFieldName,
        'name'         => $resource->name,
        'amount'       => $amount,
        'delta'        => $delta,
        'safe_storage' => round(eval('return ' . formula_parseToPHP("{$resource->saveStorage};", '$ownCaves[$caveID]'))),
        'max_level'    => round(eval('return ' . formula_parseToPHP("{$resource->maxLevel};", '$ownCaves[$caveID]')))
      );
    }
  }

  $template->addVars($resources);
}

// prepare new mail
$newMessageCount = messages_main($caveID, $ownCaves);

// prepare next and previous cave
$keys = array_keys($ownCaves);
$pos =  array_search($caveID, $keys);
$prev = isset($keys[$pos - 1]) ? $keys[$pos - 1] : $keys[count($keys)-1];
$next = isset($keys[$pos + 1]) ? $keys[$pos + 1] : $keys[0];

// set time
$UgaAggaTime = getUgaAggaTime(time());
$UgaAggaTime['month_name'] = getMonthName($UgaAggaTime['month']);

// init weather
init_Weathers();
$regions = getRegions();
$region = $regions[$ownCaves[$caveID]['regionID']];

// init vote
vote_main();

// fill it
$template->addVars(array(
  'pagetitle'     => $pagetitle,
  'showads'       => ($showads) ? true : false,
  'content'       => $content,
  'cave_id'       => $caveID,
  'cave_name'     => $ownCaves[$caveID]['name'],
  'cave_x_coord'  => $ownCaves[$caveID]['xCoord'],
  'cave_y_coord'  => $ownCaves[$caveID]['yCoord'],
  'cave_terrain'  => $ownCaves[$caveID]['terrain'],
  'time'          => date("d.m.Y H:i:s"),
  'new_mail_link' => ($newMessageCount > 0) ? '_new' : '',
  'rules_path'    => RULES_PATH,
  'help_path'     => HELP_PATH,
  'player_fame'   => $_SESSION['player']->fame,
  'weather_id'    => $weatherTypeList[$region['weather']]->weatherID,
  'weather_name'  => $weatherTypeList[$region['weather']]->name,
  'gfx'           => ($_SESSION['nogfx']) ? DEFAULT_GFX_PATH : $_SESSION['player']->gfxpath,

  'ua_time_hour'            => $UgaAggaTime['hour'],
  'ua_time_day'             => $UgaAggaTime['day'],
  'ua_time_month'           => $UgaAggaTime['month'],
  'ua_time_year'            => $UgaAggaTime['year'],
  'ua_time_time_month_name' => $UgaAggaTime['month_name'],

  'defense_link'            => DEFENSE_BUILDER,
  'defense_detail_link'     => DEFENSE_DETAIL,
  'improvement_link'        => IMPROVEMENT_BUILDER,
  'improvement_detail_link' => IMPROVEMENT_DETAIL,
  'merchant_link'           => MERCHANT,
  'messages_list_link'      => MESSAGES_LIST,
  'messages_new_link'       => MESSAGE_NEW,
  'messages_read_link'      => MESSAGE_READ,
  'ranking_player_link'     => RANKING_PLAYER,
  'ranking_tribe_link'      => RANKING_TRIBE,
  'science_link'            => SCIENCE_BUILDER,
  'unit_link'               => UNIT_BUILDER,
  'unit_detail_link'        => UNIT_DETAIL,
  'wonder_link'             => WONDER,
  'wonder_detail_link'      => WONDER_DETAIL,
));

$query_string = preg_replace('/&caveID=[^&]*/', "", $_SERVER['QUERY_STRING']);
$query_string = preg_replace('/&/', '&amp;', $query_string);

$caves = array();
if (sizeof($ownCaves)) {
  $caves['navigateCave'] = array();
  foreach ($ownCaves as $Cave) {
    $caves['navigateCave'][] = array(
      'caveID'            => $Cave['caveID'],
      'name'              => $Cave['name'],
      'x_coord'           => $Cave['xCoord'],
      'y_coord'           => $Cave['yCoord'],
      'class'             => ($caveID == $Cave['caveID']) ? 'bold' : '',
      'secure_cave'       => ($Cave['secureCave']) ? 'secureCave' : 'unsecureCave',
      'starting_position' => ($Cave['starting_position']) ? $Cave['starting_position'] : '',
      'active_name'       => $ownCaves[$caveID]['name'],
      'active_x_coord'    => $ownCaves[$caveID]['xCoord'],
      'active_y_coord'    => $ownCaves[$caveID]['yCoord'],
      'query_string'      => $query_string
    );
  }

  $template->addVars($caves);
}

$template->render();
/*
if (!is_null($prev))
  tmpl_set($template, '/PREVCAVE', array('id' => $prev, 'name' => $ownCaves[$prev]['name']));

if (!is_null($next))
  tmpl_set($template, '/NEXTCAVE', array('id' => $next, 'name' => $ownCaves[$next]['name']));
*/

// close page
page_end();

?>
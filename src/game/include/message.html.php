<?php
/*
 * message.html.php -
 * Copyright (c) 2004  OGP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

/** ensure this file is being included by a parent file */
defined('_VALID_UA') or die('Direct Access to this location is not allowed.');

require_once('modules/Contacts/model/Contacts.php');

///////////////////////////////////////////////////////////////////////////////
// MESSAGES                                                                  //
///////////////////////////////////////////////////////////////////////////////
function messages_getMessages($caveID, $deletebox, $box) {
  global $config, $template;

  // open template
  $template->setFile('messageList.tmpl');

  // init messages class
  $messagesClass = new Messages;

  // Nachrichten löschen
  $deleted = $marked_as_read = 0;

  // alte status msg?
  if ($template->getVar('status_msg')) {
    $statusMsg = $template->getVar('status_msg');
  }

  // checkboxes checked
  if (is_array($deletebox) && isset($_POST['mark_action'])) {
    $mark_action = request_var('mark_action_value', '');
    switch ($mark_action) {
      // mail and delete
      case 'mail_and_delete':
        $deleted = $messagesClass->mailAndDeleteMessages($deletebox);
        $statusMsg = array('type' => 'success', 'message' => sprintf(_('%d Nachricht(en) erfolgreich gelöscht.'), $deleted));
      break;

      // just delete
      case 'delete':
        $deleted = $messagesClass->deleteMessages($deletebox);
        $statusMsg = array('type' => 'success', 'message' => sprintf(_('%d Nachricht(en) erfolgreich gelöscht.'), $deleted));
      break;

      // mark as read
      case 'mark_as_read':
        $marked_as_read = $messagesClass->markAsRead($deletebox);
        $statusMsg = array('type' => 'success', 'message' => sprintf(_('%d Nachricht(en) als gelesen markiert.'), $deleted));
      break;
    }
  }

  // delete all
  if (isset($_POST['delete_all'])) {
    $deleted = $messagesClass->deleteAllMessages($box, request_var('messageClass', -2));
    $statusMsg = array('type' => 'success', 'message' => sprintf(_('%d Nachricht(en) erfolgreich gelöscht.'), $deleted));
  }

   
  // flag messages
  if (isset($_POST['flag'])) {
    $messagesClass->flag(request_var('id', array('' => '')));
  }

  // unflag messages
  if (isset($_POST['unflag'])) {
    $messagesClass->unflag(request_var('id', array('' => '')));
  }

  // verschiedene Boxes werden hier behandelt... //
  $boxes = array(
    BOX_INCOMING => array('boxname' => _('Posteingang'), 'from_to' => _('Absender')),
    BOX_OUTGOING => array('boxname' => _('Postausgang'), 'from_to' => _('Empfänger')),
  );

  $classes = array();
  foreach ($messagesClass->MessageClass as $id => $text) {
    $messageClass = (isset($_POST['messageClass'])) ? request_var('messageClass', 0) : 0;
    if ($id != 1001) {
      $selected = ($messageClass == $id) ? 'selected="selected"' : '';
      $classes[] = array('id' => $id, 'text' => $text, 'selected' => $selected);
    }

    //für jede Nachrichtenart wird eine eigene Box angelegt
    array_push($boxes, array($text => array('boxname' => _($text), 'von_an' => '')));
  }

  /////////////////////////////////////////////////

  // calculate offset
  $offset = request_var('offset', 0);
  $messageClass = (isset($_REQUEST['filter'])) ? request_var('messageClass', 0) : -2;
  switch ($box){
    default:
    case BOX_INCOMING:
      $message_count = $messagesClass->getIncomingMessagesCount($messageClass);
      break;
    case BOX_OUTGOING:
      $message_count = $messagesClass->getOutgoingMessagesCount($messageClass);
      break;
  }

  // offset "normalisieren"
  if ($offset < 0) {
    $offset = 0;
  }

  if ($offset > $message_count - 1) {
    $offset = $message_count;
  }

  // Nachrichten einlesen und ausgeben
  $messages = array();
  switch ($box) {
    default:
    case BOX_INCOMING:
      $messages = $messagesClass->getIncomingMessages($offset, MSG_PAGE_COUNT, $messageClass);
      break;
    case BOX_OUTGOING:
      $messages = $messagesClass->getOutgoingMessages($offset, MSG_PAGE_COUNT, $messageClass);
      break;
  }

  // vor-zurück Knopf
  $message_prev = $message_next = array();
  if ($offset - MSG_PAGE_COUNT >= 0) {
    $message_prev = array(
      'offset' => $offset - MSG_PAGE_COUNT,
      'box'    => $box,
      'modus'  => MESSAGES_LIST
    );
  }

  if ($offset + MSG_PAGE_COUNT <= $message_count - 1) {
    $message_next = array(
      'offset' => $offset + MSG_PAGE_COUNT,
      'box'    => $box,
      'modus'  => MESSAGES_LIST
    );
  }

/****************************************************************************************************
*
* Übergeben ans Template
*
****************************************************************************************************/
  $template->addVars(array(
    'from_to'         => $boxes[$box]['from_to'],
    'messages'        => $messages,
    'message_box'     => $box,
    'message_classes' => $classes,
    'message_min'     => ($message_count == 0) ? 0 : $offset + 1,
    'message_max'     => min($offset + MSG_PAGE_COUNT, $message_count),
    'message_count'   => $message_count,
    'message_prev'    => $message_prev,
    'message_next'    => $message_next,
    'status_msg'      => (isset($statusMsg)) ? $statusMsg : '',
  ));
}

///////////////////////////////////////////////////////////////////////////////
// MESSAGESDETAIL                                                            //
///////////////////////////////////////////////////////////////////////////////

function messages_showMessage($caveID, $messageID, $box) {
  
  global $no_resource_flag, $config, $template;

  // init messages class
  $messagesClass = new Messages;

  $no_resource_flag = 1;

  // open template
  $template->setFile('messageDetail.tmpl');

  if (!empty($messageID)) {
    $message = $messagesClass->getMessageDetail($messageID);

    $antworten = $contacts = $loeschen = array();
    if ($message['sender'] != "System" && $box == BOX_INCOMING){

      $template->addVars(array(
        'reply' => array(
          array('arg' => "box",        'value' => BOX_INCOMING),
          array('arg' => "betreff",    'value' => $messagesClass->createSubject($message['betreff'])),
          array('arg' => "empfaenger", 'value' => $message['sender'])
        )
      ));
      //$contacts = array('contact' => $message['sender']);
    }
    if ($message['nachrichtenart'] != 1001)
      $template->addVars(array(
        'delete' => array(
          array('arg' => "box",            'value' => $box),
          array('arg' => "deletebox[" .$messageID . "]", 'value' => $messageID)
        )
      ));
  }

  // get next and privious messageID
  $messageClass = request_var('filter', -2);

  $messageIdList = array();
  switch ($box) {
    default:
    case BOX_INCOMING:
      $messageIdList = $messagesClass->getIncomingIdList($messageClass);
      break;
    case BOX_OUTGOING:
      $messageIdList = $messagesClass->getOutgoingIdList($messageClass);
      break;
  }

  if (array_key_exists(array_search($messageID, $messageIdList)+1, $messageIdList)) {
    $template->addVar('next_msg_id', $messageIdList[array_search($messageID, $messageIdList)+1]);
  }

  if (array_key_exists(array_search($messageID, $messageIdList)-1, $messageIdList)) {
    $template->addVar('previous_msg_id', $messageIdList[array_search($messageID, $messageIdList)-1]);
  }

/****************************************************************************************************
*
* Übergeben ans Template
*
****************************************************************************************************/
  $template->addVars(array(
    'box'           => $box,
    'message'       => $message,
    'message_class' => $messageClass,
    'message_id'    => $messageID,
    'message_img'   => (isset($config->messageImage[$message['nachrichtenart']])) ? $config->messageImage[$message['nachrichtenart']] : '',
    'xml_button'    => (empty($message['messageXML'])) ? false : true,
  ));
}

///////////////////////////////////////////////////////////////////////////////
// NEW_MESSAGE                                                               //
///////////////////////////////////////////////////////////////////////////////

function messages_newMessage($caveID) {
  global $config, $template, $no_resource_flag;
  $no_resource_flag = 1;

  // get contacts model
  $contacts_model = new Contacts_Model();
  $contacts = $contacts_model->getContacts();

  // open template
  $template->setFile('messageDialogue.tmpl');

  $template->addVars(array(
    'box'        => request_var('box', BOX_INCOMING),
    'sender'     => $_SESSION['player']->name,
    'empfaenger' => unhtmlentities(request_var('empfaenger', "")),
    'betreff'    => request_var('betreff', ""),
    'contacts'   => $contacts,
    'hidden'     => array(
      array('arg' => "box",    'value' => request_var('box', BOX_INCOMING)),
      array('arg' => "caveID", 'value' => $caveID),
      array('arg' => "modus",  'value' => NEW_MESSAGE_RESPONSE)
    ),
  ));
}

///////////////////////////////////////////////////////////////////////////////
// NEW_MESSAGE_RESPONSE                                                      //
///////////////////////////////////////////////////////////////////////////////

function messages_sendMessage($caveID) {
  global $config, $template, $no_resource_flag;

  // init messages class
  $messagesClass = new Messages;

  $no_resource_flag = 1;
  $zeichen = 16384;

  $betreff = request_var('betreff', "");
  $nachricht = request_var('nachricht', "");

  // **** get recipient ****
  $contactID = request_var('contactID', 0);

  // get recipient from contactlist
  $empfaenger = "";
  if ($contactID > 0) {
    // get contacts model
    $contacts_model = new Contacts_Model();
    $contact = $contacts_model->getContact($contactID);
    $empfaenger = $contact['contactname'];

  // get recipient from textfield
  } else {
    $empfaenger = request_var('empfaenger', "");
  }

  if ($betreff == "") $betreff = _('&lt;leer&gt;');

  // open template
  $template->setFile('messageDialogue.tmpl');

  if (strlen($nachricht) > $zeichen) {
    $message = array('type' => 'error', 'message' => sprintf(_('Fehler! Nachricht konnte nicht verschickt werden! Stellen Sie sicher, dass die Nachricht nicht länger als %d Zeichen ist.'), $zeichen));

    $template->addVars(array(
      'box'        => request_var('box', BOX_INCOMING),
      'status_msg' => $message,
      'sender'     => $_SESSION['player']->name,
      'empfaenger' => $empfaenger,
      'betreff'    => $betreff,
      'nachricht'  => $nachricht,
      'contacts'   => $contacts,
      'hidden'     => array(
        array('arg' => "box",    'value' => request_var('box', BOX_INCOMING)),
        array('arg' => "caveID", 'value' => $caveID),
        array('arg' => "modus",  'value' => NEW_MESSAGE_RESPONSE)
      ),
    ));
  }

  if ($messagesClass->insertMessageIntoDB($empfaenger, $betreff, $nachricht)) {
    $template->addVar('status_msg', array('type' => 'success', 'message' => _('Ihre Nachricht wurde verschickt!')));
    messages_getMessages($caveID, 0, BOX_INCOMING);
    return;
  } else {
    $message = array('type' => 'error', 'message' => _('Fehler! Nachricht konnte nicht verschickt werden! Stellen Sie sicher, dass es den angegebenen Empfänger gibt.'));

    $template->addVars(array(
      'box'        => request_var('box', BOX_INCOMING),
      'status_msg' => $message,
      'sender'     => $_SESSION['player']->name,
      'empfaenger' => $empfaenger,
      'betreff'    => $betreff,
      'nachricht'  => $nachricht,
      'contacts'   => $contacts,
      'hidden'     => array(
        array('arg' => "box",    'value' => request_var('box', BOX_INCOMING)),
        array('arg' => "caveID", 'value' => $caveID),
        array('arg' => "modus",  'value' => NEW_MESSAGE_RESPONSE)
      ),
    ));
  }
}

?>
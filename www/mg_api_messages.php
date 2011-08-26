<?php
/**
 * This file retrieves entries in the user's message queue. It is outside the Yii framework 
 * to improve performance. This file will be queried many times a minute on multiplayer games
 * and each overhead would be an performance hit. 
 * 
 * It will respond in JSON as it is part of the api.
 *
 * @author Vincent Van Uffelen
 */
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$messages = array();

// xxx add check for shared secret in session. 
// make sure it is using only post request
// if set then open db. select on message table and query all messages for 
// this shared secret and the current session id.
// loop through these remove them from the message queue and return the found messages. 
echo json_encode(array('messages' => $messages));
<?php

elgg_load_js('friend_collections_message.js');

$subject = get_input('subject');
$message = get_input('message');
$id = get_input('id');

$members = get_members_of_access_collection($id, true);

// show a preview of the email
echo '<label>' . elgg_echo('friend_collections_message:label:preview') . '</label><br><br>';

echo elgg_view_module('info', $subject, $message);

echo '<br><br>';

echo '<label>' . elgg_echo('friend_collections_message:label:recipients') . '</label><br>';

echo elgg_view('input/tokeninput', array(
	'value' => $members,
	'name' => 'recipients',
	'callback' => 'friend_collections_message_picker_callback',
	'query' => array('id' => $id),
	'multiple' => true,
));

echo '<br><br>';

echo elgg_view('input/hidden', array('name' => 'subject', 'value' => $subject));
echo elgg_view('input/hidden', array('name' => 'message', 'value' => $message));
echo elgg_view('input/hidden', array('name' => 'id', 'value' => $id));
echo elgg_view('input/submit', array('value' => elgg_echo('friend_collections_message:send')));
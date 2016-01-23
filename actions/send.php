<?php

elgg_make_sticky_form('friend_collection_message');

$id = get_input('id');
$recipients = get_input('recipients');
if (is_string($recipients)) {
    $recipients = explode(',', $recipients);
}

$subject = get_input('subject');
$message = get_input('message');

if (!$subject || !$message) {
    register_error(elgg_echo('friend_collection_message:missing_fields'));
    forward(REFERER);
}

$collection = get_access_collection($id);
$owner = get_user($collection->owner_guid);

if (!$collection || !$owner || !$owner->canEdit()) {
    register_error(elgg_echo('friend_collection_message:invalid_id'));
    forward(REFERER);
}

if (!$recipients) {
	register_error(elgg_echo('friend_collection_message:no:recipients'));
	forward(REFERER);
}

// sanity check has passed
// set our handler for vvvvrrrroooooooooom
elgg_register_event_handler('shutdown', 'system', 'friend_collection_message_shutdown_tasks');

// set our variables in config to be used in our shutdown handler
elgg_set_config('friend_collection_message_id', $id);
elgg_set_config('friend_collection_message_recipients', $recipients);
elgg_set_config('friend_collection_message_subject', $subject);
elgg_set_config('friend_collection_message_message', $message);

system_message(elgg_echo('friend_collection_message:message:sent'));
elgg_clear_sticky_form('friend_collection_message');
forward(REFERER);

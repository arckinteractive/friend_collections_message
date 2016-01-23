<?php

$collection = false;
if (isset($vars['entity']->friend_collection)) {
    $collection = get_access_collection($vars['entity']->friend_collection);
}
$owner = $vars['entity']->getOwnerEntity();

if ($collection && $owner->canEdit()) {
	
	$can_message = elgg_trigger_plugin_hook('can_message', 'collection', array('collection_id' => $collection->id), true);
	
	if (!$can_message) {
		return;
	}
	
    $text = elgg_view_icon('mail') . '&nbsp;' . elgg_echo('friend_collection_message:widget:send');
    $href = elgg_normalize_url('friends/collections/message/' . $vars['entity']->friend_collection);
    
    $link = elgg_view('output/url', array(
        'text' => $text,
        'href' => $href,
        'is_trusted' => true,
        'encode_text' => false,
    ));
    
    echo '<br>';
    echo $link;
}

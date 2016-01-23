<?php

function friend_collection_message_init() {
    elgg_extend_view('widgets/friend_collections/content', 'friend_collection_message/widget');
    
    elgg_register_plugin_hook_handler('route', 'friends', 'friend_collection_message_router', 0);
    
    elgg_register_action('friend_collection_message/send', dirname(__FILE__) . '/actions/send.php');
}


/**
 * add in our own page in the friends/collections URI
 * 
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 * @return boolean
 */
function friend_collection_message_router($hook, $type, $return, $params) {
    if (!($return['segments'][0] == 'collections' && $return['segments'][1] == 'message')) {
        return $return;
    }
    
    $id = $return['segments'][2];
    $collection = get_access_collection($id);
    $owner = get_user($collection->owner_guid);
	$can_message = elgg_trigger_plugin_hook('can_message', 'collection', array('collection_id' => $id), true);
    
    if (!$collection || !$owner || !$owner->canEdit() || !$can_message) {
        return $return;
    }
    
	$step = get_input('step', 1);
	// if we don't havea subject/message we will force step 1
	$subject = get_input('subject');
	$message = get_input('message');
	if (!$subject || !$message) {
		$step = 1;
	}
	
    $title = elgg_echo('friend_collection_message:title', array($collection->name));
    $collections_link = elgg_normalize_url('collections/' . $owner->username);
    elgg_push_breadcrumb(elgg_echo('friends:collections'), $collections_link);
    elgg_push_breadcrumb($title);
	
	switch ($step) {
		case 2:
			$action = 'action/friend_collection_message/send';
			$content = elgg_view_form('friend_collection_message/send', array(
				'action' => $action
			), array(
				'collection' => $collection
			));
			break;
		
		default:
			$action = elgg_http_remove_url_query_element(current_page_url(), 'step');
			$action = elgg_http_add_url_query_elements($action, array('step' => 2));
			
			$content = elgg_view_form('friend_collection_message/compose', array(
				'action' => $action
			), array(
				'collection' => $collection
			));
			break;
	}
        
    
    $layout = elgg_view_layout('content', array(
        'title' => $title,
        'content' => $content,
        'filter' => false
    ));
    
    echo elgg_view_page($title, $layout);
    
    return true;
}


/**
 * send the message in the vroom shutdown stage
 */
function friend_collection_message_shutdown_tasks() {
    $id = elgg_get_config('friend_collection_message_id');
	$recipients = elgg_get_config('friend_collection_message_recipients');
    $subject = elgg_get_config('friend_collection_message_subject');
    $message = elgg_get_config('friend_collection_message_message');
    
    $members = get_members_of_access_collection($id, true);
    
	$guids = array_intersect($recipients, $members);
	
    notify_user(
            $guids,
            elgg_get_logged_in_user_guid(),
            $subject,
            $message
    );
}


function friend_collections_message_picker_callback($query, $options = array()) {
	$id = sanitize_int(get_input('id'));
	$guids = get_members_of_access_collection($id, true);
	// replace mysql vars with escaped strings
    $q = str_replace(array('_', '%'), array('\_', '\%'), $query);

	if (!$guids || !$id) {
		return array();
	}
	
	$dbprefix = elgg_get_config('dbprefix');
	return elgg_get_entities(array(
		'type' => 'user',
		'joins' => array(
			"JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid",
			"JOIN {$dbprefix}access_collection_membership acm ON acm.user_guid = e.guid"
		),
		'wheres' => array(
			"ue.username LIKE '%{$q}%' OR ue.name LIKE '%{$q}%'",
			"acm.access_collection_id = $id"
		),
		'order_by' => 'ue.name ASC'
	));
}

elgg_register_event_handler('init', 'system', 'friend_collection_message_init');

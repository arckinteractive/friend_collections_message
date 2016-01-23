<?php

echo elgg_view('output/longtext', array(
    'value' => elgg_echo('friend_collection_message:form:help'),
    'class' => 'elgg-subtext'
));

/*
$members = get_members_of_access_collection($vars['collection']->id);
$value = get_members_of_access_collection($vars['collection']->id, true);
echo '<label>' . elgg_echo('friend_collection_message:label:to') . '</label>';
echo elgg_view('input/friendspicker', array(
	'name' => 'recipients',
	'entities' => $members,
	'value' => $value
));
 * 
 */

echo '<label>' . elgg_echo('friend_collection_message:label:subject') . '</label>';
echo elgg_view('input/text', array(
    'name' => 'subject',
    'value' => elgg_get_sticky_value('friend_collection_message', 'subject')
));

echo '<br><br>';


echo '<label>' . elgg_echo('friend_collection_message:label:message') . '</label>';
echo elgg_view('input/longtext', array(
    'name' => 'message',
    'value' => elgg_get_sticky_value('friend_collection_message', 'message')
));

echo '<br><br>';

echo '<div class="elgg-foot">';
echo elgg_view('input/hidden', array('name' => 'id', 'value' => $vars['collection']->id));
echo elgg_view('input/submit', array('value' => elgg_echo('next')));
echo '</div>';

elgg_clear_sticky_form('friend_collection_message');

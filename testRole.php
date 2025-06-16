<?php

require_once 'util/rbac/Role.php';
require_once 'util/rbac/RoleModelHandler.php';
require_once 'util/Database.php';

// Test saving a Role with all properties filled

// Create a new Role object
/*$role = new Role('Admin');

// Allow permissions on various resources
$role->allow('*', '*', '*'); // Allow all actions on all items of all resources
$role->allow('*', 'article', '*'); // Allow all actions on all items of the 'article' resource
$role->allow('view', 'article', '*'); // Allow 'view' action on all items of the 'article' resource
$role->allow('*', 'article', '1'); // Allow all actions on item '1' of the 'article' resource
$role->allow('delete', 'article', '1'); // Allow 'delete' action on item '1' of the 'article' resource

// Check inheriteing permissions from other roles
$adminRole = new Role('group_admin');
$adminRole->allow('*', '*', '*');

$modRole = new Role('group_moderator');
$modRole->allow('*', 'article', '*');

$user1Role = new Role('user_1');
$user1Role->inherite($adminRole);

$user2Role = new Role('user_2');
$user2Role->inherite($modRole);

// Check permissions for user 1 and user 2
var_dump($user1Role->isAllowed('create', 'article')); // Should return true
echo '<br>';
var_dump($user2Role->isAllowed('delete', 'article', '1')); // Should return true
echo '<br>';
var_dump($user2Role->isAllowed('create', 'category')); // Should return false
echo '<br>';
// inherite permissions on specific items
$adminRole = new Role('group_admin');
$adminRole->allow('*', '*', '*');

$modRole = new Role('group_moderator');
$modRole->allow('*', 'article', '*');

$user1Role = new Role('user_1');
$user1Role->specificRole($adminRole, 'article', '3'); // user_1 has admin permission on article with ID=3 only

var_dump($user1Role->isAllowed('edit', 'article', '3')); // Should return true
echo '<br>';
var_dump($user1Role->isAllowed('delete', 'article', '3')); // Should return true
echo '<br>';
var_dump($user1Role->isAllowed('edit', 'article', '1')); // Should return false
echo '<br>';
// Parse resource information
$group = new Role('author');
$group->allow('*', 'book', 1);
$group->allow('view', 'article', '*');

$user = new Role('user');
$user->allow('view', 'book', 3);
$user->allow('view', 'book', 4);
$user->deny('view', 'book', 5);
$user->inherite($group);

$result = $user->parseResourceInfo('book', 'view');
echo '<br>User can view books with IDs: '.implode(',', $result['allowed'])."\n";
echo '<br>User is denied to view books with IDs: '.implode(',', $result['denied'])."\n";

$result = $user->parseResourceInfo('article', 'view');
echo '<br>User can view articles with IDs: '.implode(',', $result['allowed'])."\n";
echo '<br>User is denied to view articles with IDs: '.implode(',', $result['denied'])."\n";

$result = $user->parseResourceInfo('category', 'view');
echo '<br>User can view categories with IDs: '.implode(',', $result['allowed'])."\n";
echo '<br>User is denied to view categories with IDs: '.implode(',', $result['denied'])."\n";
*/
// Create a RoleDBHandler instance
$roleModelHandler = new RoleModelHandler(DBConnection::getConnection());
/*
// Save the Role to the database
$roleModelHandler->saveRole($role);
$roleModelHandler->saveRole($adminRole);
$roleModelHandler->saveRole($user);
$roleModelHandler->saveRole($group);
$roleModelHandler->saveRole($user1Role);
$roleModelHandler->saveRole($user2Role);
$roleModelHandler->saveRole($modRole);
*/
/*$myRole = $roleModelHandler->getRoleByName('user');
echo '<br><pre>'. var_dump($myRole->isAllowed('delete','post',null));
echo '<br><pre>'.var_dump($myRole->parseResourceInfo('book', null));
*/
$myRole = new Role('test@3');
$roleModelHandler->saveRole($myRole);
echo '<br>Role saved successfully!';

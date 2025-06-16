<?php

require_once 'TokenHandler.php';
require_once 'Database.php';
require_once 'config/Constants.php';
require_once 'rbac/Role.php';
require_once 'rbac/RoleModelHandler.php';

/**
 *  PermissionHandler Class
 *  Manage the Permissions on the API
 *  verifying Allowed resources on requests by Authenticated User.
 *
 *  @author Joshua Newman
 *  @copyright 2024-04
 *
 *  @version 1.0
 */
class PermissionHandler
{
    public static function hasPermissionOn($resourceType, $action = null, $resourceId = null): bool
    {
        if (Constants::JWT['TOKEN_AUTHENTICATION']) {
            
            $user = TokenHandler::getJWTData();
            $roleHandler = new RoleModelHandler(Database::getConnection());
            $userRole = $roleHandler->getRoleByName($user->email);

            if ($userRole) {
                return $userRole->isAllowed($action, $resourceType, $resourceId);
            }
            return false;
        }
        
        return true;
    }
}

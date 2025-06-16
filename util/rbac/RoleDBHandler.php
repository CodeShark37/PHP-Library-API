<?php
/**
 *  RoleDBHandler Class
 *  provide a way to perform a quickly and direct management of Roles in Database.
 *  @author Joshua Newman
 *  @copyright 2024-04
 *  @version 1.0
 */
class RoleDBHandler
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Insert a new role
    public function insertRole($roleName)
    {
        if ($this->checkRole($roleName)) {
            return false; // Role already exists
        }
        $query = 'INSERT INTO role (name) VALUES (:name)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);

        return $stmt->execute();
    }

    // Update role name
    public function updateRoleName($oldRoleName, $newRoleName)
    {
        if (!$this->checkRole($oldRoleName)) {
            return false; // Role doesn't exist
        }
        $query = 'UPDATE role SET name = :new_name WHERE name = :old_name';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':old_name', $oldRoleName);
        $stmt->bindValue(':new_name', $newRoleName);

        return $stmt->execute();
    }

    // Delete a role and your attributes
    public function deleteRole($roleName)
    {
        if (!$this->checkRole($roleName) || $this->checkInherited($roleName) || $this->checkSpecific($roleName)) {
            return false; // Role doesn't exist and is an foreign key
        }
        // Cascade delete from related tables
        $this->deleteResource($roleName);
        $this->deleteInherited($roleName);
        $this->deleteSpecific($roleName);

        $query = 'DELETE FROM role WHERE name = :name';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);
        $stmt->execute();
        
        return true;
    }

    // Insert resource for a role
    public function insertResource($roleName, $resource)
    {
        $roleId = $this->getRoleIdByName($roleName);
        if (is_null($roleId)) {
            return false; // Role doesn't exist
        }

        // Validate the structure of the resource array
        if (!isset($resource['resource_type'])) {
            return false; // Missing resource_type
        }

        // Set defaults for optional fields
        $resourceId = $resource['resource_id'] ?? '*';
        $action = $resource['action'] ?? '*';
        $allowed = $resource['allowed'] ?? true;

        $query = 'INSERT INTO role_resource (role_id, resource_type, resource_id, action, allowed) VALUES (:role_id, :resource_type, :resource_id, :action, :allowed)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':resource_type', $resource['resource_type']);
        $stmt->bindValue(':resource_id', $resourceId);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':allowed', $allowed, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    // Update resource for a role
    public function updateResource($roleName, $oldResource, $newResource)
    {
        $roleId = $this->getRoleIdByName($roleName);
        if (is_null($roleId)) {
            return false; // Role doesn't exist
        }

        // Validate the structure of the resource arrays
        if (!isset($oldResource['resource_type']) || !isset($newResource['resource_type'])) {
            return false; // Missing resource
        }

        $oldResourceType = $oldResource['resource_type'];
        $newResourceType = $newResource['resource_type'];
        $oldResourceId = $oldResource['resource_id'] ?? null;
        $oldAction = $oldResource['action'] ?? null;

        $newResourceId = $newResource['resource_id'] ?? '*';
        $newAction = $newResource['action'] ?? '*';

        $query = 'UPDATE role_resource SET resource_type = :new_resource_type, resource_id = :new_resource_id, action = :new_action WHERE role_id = :role_id AND resource_type = :old_resource_type AND resource_id = :old_resource_id AND action= :old_action';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':old_resource_type', $oldResourceType);
        $stmt->bindValue(':old_resource_id', $oldResourceId);
        $stmt->bindValue(':old_action', $oldAction);
        $stmt->bindValue(':new_resource_type', $newResourceType);
        $stmt->bindValue(':new_resource_id', $newResourceId);
        $stmt->bindValue(':new_action', $newAction);

        return $stmt->execute();
    }

    // Delete resource for a role
    public function deleteResource($roleName, $resource = null)
    {
        $roleId = $this->getRoleIdByName($roleName);
        if (is_null($roleId)) {
            return false; // Role doesn't exist
        }

        $resourceType = $resource['resource_type'] ?? null;
        $resourceId = $resource['resource_id'] ?? null;
        $action = $resource['action'] ?? null;

        $query = 'DELETE FROM role_resource WHERE role_id = :role_id';
        if (!is_null($resourceType) && !is_null($resourceId) && !is_null($action)) {
            $query .= ' AND resource_id = :resource_id AND action = :action AND resource_type = :resource_type';
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        if (!is_null($resourceType) && !is_null($resourceId) && !is_null($action)) {
            $stmt->bindValue(':resource_id', $resourceId);
            $stmt->bindValue(':resource_type', $resourceType);
            $stmt->bindValue(':action', $action);
        }

        return $stmt->execute();
    }

    // Insert inherited role for a role
    public function insertInherited($roleName, $inheritedRole)
    {
        $roleId = $this->getRoleIdByName($roleName);
        $inheritedRoleId = $this->getRoleIdByName($inheritedRole);
        if (is_null($roleId) || is_null($inheritedRoleId)) {
            return false; // Role(s) doesn't exist
        }
        $query = 'INSERT INTO role_inheritance (role_id, inherited_role_id) VALUES (:role_id, :inherited_role_id)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':inherited_role_id', $inheritedRoleId);

        return $stmt->execute();
    }

    // Update inherited role for a role
    public function updateInherited($roleName, $oldInheritedRoleName, $newInheritedRoleName)
    {
        $roleId = $this->getRoleIdByName($roleName);
        $oldInheritedRoleId = $this->getRoleIdByName($oldInheritedRoleName);
        $newInheritedRoleId = $this->getRoleIdByName($newInheritedRoleName);
        if (is_null($roleId) || is_null($oldInheritedRoleId) || is_null($newInheritedRoleId)) {
            return false; // Inherited(s) doesn't exist
        }
        $query = 'UPDATE role_inheritance SET inherited_role_id = :new_inherited_role_id WHERE role_id = :role_id AND inherited_role_id = :old_inherited_role_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':old_inherited_role_id', $oldInheritedRoleId);
        $stmt->bindValue(':new_inherited_role_id', $newInheritedRoleId);
        
        return $stmt->execute();
    }

    // Delete inherited roles
    public function deleteInherited($roleName, $inheritedRoleName = null)
    {
        $roleId = $this->getRoleIdByName($roleName);
        if (is_null($roleId)) {
            return false; // Role doesn't exist
        }
        $query = 'DELETE FROM role_inheritance WHERE role_id = :role_id ';
        $inheritedRoleId = $this->getRoleIdByName($inheritedRoleName);
        if (!is_null($inheritedRoleId)) {
            $query .= 'AND inherited_role_id = :inherited_role_id';
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        if (!is_null($inheritedRoleId)) {
            $stmt->bindValue(':inherited_role_id', $inheritedRoleId);
        }

        return $stmt->execute();
    }

    // Insert specific role for a role
    public function insertSpecific($roleName, $roleSpecific, $resourceType, $resourceId)
    {
        $roleId = $this->getRoleIdByName($roleName);
        $specificRoleId = $this->getRoleIdByName($roleSpecific);
        if (is_null($roleId) || is_null($specificRoleId)) {
            return false; // Role(s) doesn't exist
        }
        $query = 'INSERT INTO specific_role (role_id, specific_role_id, resource_type, resource_id) VALUES (:role_id, :specific_role_id, :resource_type, :resource_id)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':specific_role_id', $specificRoleId);
        $stmt->bindValue(':resource_type', $resourceType);
        $stmt->bindValue(':resource_id', $resourceId);

        return $stmt->execute();
    }

    // Update specific role for a role
    public function updateSpecific($roleName, $oldSpecificRole, $newSpecificRole, $oldSpecificResource, $newSpecificResource)
    {
        $roleId = $this->getRoleIdByName($roleName);
        $oldSpecificRoleId = $this->getRoleIdByName($oldSpecificRole);
        $newSpecificRoleId = $this->getRoleIdByName($newSpecificRole);
         
        if (is_null($roleId) || is_null($oldSpecificRoleId) || is_null($newSpecificRoleId) || 
             is_null($oldSpecificResource) || is_null($newSpecificResource)) {
            return false; // Role(s) and update resources doesn't exist  
        }

        $oldResourceType   = $oldSpecificResource['resource_type'] ?? null;
        $oldResourceId     = $oldSpecificResource['resource_id'] ?? null;
        $newResourceType   = $newSpecificResource['resource_type'] ?? null;
        $newResourceId     = $newSpecificResource['resource_id'] ?? null;
        
        //verify if a new specific resource was previously inserted on role_resource table
        if(!$this->checkResource($newSpecificRole, ['resource_type'=>$newResourceType]) && !$this->checkResource($newSpecificRole, ['resource_type'=>$newResourceType,'resource_id'=>$newResourceId])){
            return false;
        }

        $query = 'UPDATE specific_role SET specific_role_id = :new_specific_role_id, resource_type = :new_resource_type, resource_id = :new_resource_id WHERE role_id = :role_id AND specific_role_id = :old_specific_role_id AND resource_type = :old_resource_type AND resource_id = :old_resource_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':old_specific_role_id', $oldSpecificRoleId);
        $stmt->bindValue(':old_resource_type', $oldResourceType);
        $stmt->bindValue(':old_resource_id', $oldResourceId);
        $stmt->bindValue(':new_specific_role_id', $newSpecificRoleId);
        $stmt->bindValue(':new_resource_type', $newResourceType);
        $stmt->bindValue(':new_resource_id', $newResourceId);

        return $stmt->execute();
    }

    // Delete specific role for a role
    public function deleteSpecific($roleName, $roleSpecific=null, $resourceType = null, $resourceId = null)
    {
        $roleId = $this->getRoleIdByName($roleName);
        $specificRoleId = $this->getRoleIdByName($roleSpecific);
        if (is_null($roleId)) {
            return false; // Role(s) doesn't exist
        }
        
        $query = 'DELETE FROM specific_role WHERE role_id = :role_id ';
        //for deleting about specificRole
        if(!is_null($specificRoleId)){
            $query .= 'AND specific_role_id = :specific_role_id '; 
            //for deleting about resourceType and resourceId
            if(!is_null($resourceType) && !is_null($resourceId)){
                $query .= 'AND resource_type = :resource_type AND resource_id = :resource_id';
            }
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        if(!is_null($specificRoleId)){
            $stmt->bindValue(':specific_role_id', $specificRoleId);
            if(!is_null($resourceType) && !is_null($resourceId)){
                $stmt->bindValue(':resource_type', $resourceType);
                $stmt->bindValue(':resource_id', $resourceId);
            }
        }
        

        return $stmt->execute();
    }

    // Check if a role has access to a specific resource
    public function checkResource($roleName, $resource)
    {
        $roleId = $this->getRoleIdByName($roleName);
        if (is_null($roleId)) {
            return false; // Role doesn't exist
        }

        // Validate the structure of the resource array
        if (!isset($resource['resource_type'])) {
            return false; // Missing resource_type
        }

        $resourceType = $resource['resource_type'];
        $resourceId = $resource['resource_id'] ?? '*';
        $action = $resource['action'] ?? '*';
        $allowed = $resource['allowed'] ?? 1;
        $query = 'SELECT COUNT(*) FROM role_resource WHERE role_id = :role_id AND resource_type = :resource_type AND (resource_id IS NULL OR resource_id = :resource_id) AND (action IS NULL OR action = :action) AND allowed = :allowed ';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':resource_type', $resourceType);
        $stmt->bindValue(':resource_id', $resourceId);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':allowed', $allowed);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Check if a role exists
    public function checkRole($roleName)
    {
        $query = 'SELECT COUNT(*) FROM role WHERE name = :name';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
    // Check if a role is an inheritedRole
    protected function checkInherited($roleName)
    {
        $query = 'SELECT COUNT(*) FROM role_inheritance WHERE inherited_role_id = :inheritedRoleId';
        $stmt = $this->db->prepare($query);
        $inheritedRoleId = $this->getRoleIdByName($roleName); 
        $stmt->bindValue(':inheritedRoleId', $inheritedRoleId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Check if a role is an specificRole
    protected function checkSpecific($roleName)
    {
        $query = 'SELECT COUNT(*) FROM specific_role WHERE specific_role_id = :specificRoleId';
        $stmt = $this->db->prepare($query);
        $specificRoleId = $this->getRoleIdByName($roleName); 
        $stmt->bindValue(':specificRoleId', $specificRoleId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Get role ID by name
    protected function getRoleIdByName($roleName)
    {
        $query = 'SELECT id FROM role WHERE name = :name';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['id'] : null;
    }
    
}

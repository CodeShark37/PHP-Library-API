<?php

/**
 *  RoleModelHandler Class
 *  fetch the Roles from Database to Role Model Object 
 *  and save the Role Model Object to Database.
 *  @author Joshua Newman
 *  @copyright 2024-04
 *  @version 1.0
 */

class RoleModelHandler {
    protected $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function saveRole(Role $role){
        // Check if the role already exists in the database
        $existingRoleId = $this->fetchRoleIdByName($role->getName());
    
        // If the role already exists, update its properties instead of inserting a new one
        if(!is_null($existingRoleId)){
            //$this->updateRoleResources($existingRoleId, $role->getResources());
            $this->updateInheritedRoles2($existingRoleId, $role->getInheritedRoles());
            //$this->updateSpecificRoles($existingRoleId, $role->getSpecificRoles());
        } else {
            // If the role does not exist, insert a new one
            $roleId = $this->insertRole($role->getName());
            $this->insertRoleResources($roleId, $role->getResources());
            $this->insertInheritedRoles($roleId, $role->getInheritedRoles());
            $this->insertSpecificRoles($roleId, $role->getSpecificRoles());
        }
        return true;
    }
    // Insert a role into the roles table and return its ID
    protected function insertRole($name){
        $query = "INSERT INTO role (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Insert resources associated with a role
    protected function insertRoleResources($roleId, $resources){
        foreach($resources as $resourceType => $resourceIds){
            foreach($resourceIds as $resourceId => $actions){
                foreach($actions as $action => $allowed){
                    $this->insertResource($roleId, $resourceType, $resourceId, $action, $allowed);
                }
            }
        }
    }

    // Insert a resource associated with a role into the role_resources table
    protected function insertResource($roleId, $resourceType, $resourceId, $action, $allowed){
        $query = "INSERT INTO role_resource (role_id, resource_type, resource_id, action, allowed)
                  VALUES (:role_id, :resource_type, :resource_id, :action, :allowed)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->bindValue(':resource_type', $resourceType);
        $stmt->bindValue(':resource_id', $resourceId);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':allowed', $allowed ? 1 : 0);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Insert inherited roles associated with a role
    protected function insertInheritedRoles($roleId, $inheritedRoles){
        foreach($inheritedRoles as $inheritedRole){
            $this->insertInheritedRole($roleId, $inheritedRole->getName());
        }
    }

    // Insert an inherited role into the role_inheritance table
    protected function insertInheritedRole($roleId, $inheritedRoleName){
        $inheritedRoleId = $this->fetchRoleIdByName($inheritedRoleName);
        if($inheritedRoleId !== null){
            $query = "INSERT INTO role_inheritance (role_id, inherited_role_id) VALUES (:role_id, :inherited_role_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':role_id', $roleId);
            $stmt->bindValue(':inherited_role_id', $inheritedRoleId);
            $stmt->execute();
            return $this->db->lastInsertId();
        }
    }

    // Insert specific roles associated with a role
    protected function insertSpecificRoles($roleId, $specificRoles){
        foreach($specificRoles as $specificRole){
            $this->insertSpecificRole($roleId, $specificRole['role']->getName(), $specificRole['resource'], $specificRole['resource_id']);
        }
    }

    // Insert a specific role into the specific_role table
    protected function insertSpecificRole($roleId, $specificRoleName, $resourceType, $resourceId){
        $specificRoleId = $this->fetchRoleIdByName($specificRoleName);
        if($specificRoleId !== null){
            $query = "INSERT INTO specific_role (role_id, specific_role_id, resource_type, resource_id)
                      VALUES (:role_id, :specific_role_id, :resource_type, :resource_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':role_id', $roleId);
            $stmt->bindValue(':specific_role_id', $specificRoleId);
            $stmt->bindValue(':resource_type', $resourceType);
            $stmt->bindValue(':resource_id', $resourceId);
            $stmt->execute();
            return $this->db->lastInsertId();
        }
    }

    // Retrieve a Role from the database by name
    public function getRoleByName($roleName){
        $roleData = $this->fetchRoleByName($roleName);

        if(!$roleData){
            return null;
        }

        // Initialize a Role object
        $role = new Role($roleData['name']);

        // Fetch resources associated with the role
        $resourcesData = $this->fetchRoleResources($roleData['id']);
        foreach($resourcesData as $resource){
            $allowed = $resource['allowed'] == 1 ? true : false;
            if($allowed){
                $role->allow($resource['action'], $resource['resource_type'], $resource['resource_id']);
            }else {
                $role->deny($resource['action'], $resource['resource_type'], $resource['resource_id']);
            }
        }

        // Fetch inherited roles
        $inheritedRolesData = $this->fetchInheritedRoles($roleData['id']);
        foreach($inheritedRolesData as $inheritedRoleId){
            $inheritedRoleName = ($this->fetchRoleById($inheritedRoleId))['name'];
            $inheritedRole = $this->getRoleByName($inheritedRoleName);
            if($inheritedRole){
                $role->inherite($inheritedRole);
            }
        }

        // Fetch specific roles
        $specificRolesData = $this->fetchSpecificRoles($roleData['id']);
        foreach($specificRolesData as $specificRole){
            $specificRoleName = ($this->fetchRoleById($specificRole['specific_role_id']))['name'];
            $specificRoleObj = $this->getRoleByName($specificRoleName);
            if($specificRoleObj){
                $role->specificRole($specificRoleObj, $specificRole['resource_type'], $specificRole['resource_id']);
            }
        }    

        return $role;
    }

    // Fetch role details by name
    protected function fetchRoleByName($roleName){
        $query = "SELECT * FROM role WHERE name = :name";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch role details by ID
    protected function fetchRoleById($roleId){
        $query = "SELECT name FROM role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $roleId,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch role resources by id
    protected function fetchRoleResources($roleId){
        $query = "SELECT * FROM role_resource WHERE role_id = :role_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id',$roleId,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch inherited roles associated with a role by ID
    protected function fetchInheritedRoles($roleId){
        $query = "SELECT inherited_role_id FROM role_inheritance WHERE role_id = :role_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Fetch specific roles associated with a role by ID
    protected function fetchSpecificRoles($roleId){
        $query = "SELECT specific_role_id, resource_type, resource_id FROM specific_role WHERE role_id = :role_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId,PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get role ID by name
    public function fetchRoleIdByName($roleName){
        $role = $this->fetchRoleByName($roleName);
        return $role ? $role['id'] : null;
    }
     // Get roleName by ID
    public function fetchRoleNameById($roleId){
        $role = $this->fetchRoleByName($roleId);
        return $role ? $role['name'] : null;
    }
    //---------------------------
    protected function updateInheritedRoles2($roleId, $inheritedRoles){
        //transform the inherited roles names to id's
        $inheritedRoles = array_map(function($value){
            return $this->fetchRoleIdByName($value);
        },array_keys($inheritedRoles));
        //search for inherited roles that's absent from DB to delete them's
        $arr_delete = array_diff_key(fetchInheritedRoles($roleId),$inheritedRoles);
        print_r($arr_delete);

        $arr_insert = array_diff_key($inheritedRoles,fetchInheritedRoles($roleId));
        print_r($arr_insert);

        //delete absen't inherited roles
        foreach ($arr_delete as $value) {
            $this->deleteInheritedRoles($roleId,$value);
        }
       //insert new inherited roles
       foreach ($arr_insert as $value) {
            $this->insertInheritedRoles($roleId,fetchRoleNameById($value));
        }
    }
    // Update inherited roles
    protected function updateInheritedRoles($roleId, $inheritedRoles){
        // Delete existing inherited roles for the role
        $this->deleteInheritedRoles($roleId);
        // Insert new inherited roles
        $this->insertInheritedRoles($roleId, $inheritedRoles);
    }

    // Delete existing all inherited roles or an specific
    protected function deleteInheritedRoles($roleId,$inheritedRoleId=null){
        $query = "DELETE FROM role_inheritance WHERE role_id = :role_id";
        if (!empty($inheritedRoleId)) {
            $query .= " AND inherited_role_id = :inherited_role_id";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        if (!empty($inheritedRoleId)) {
            $stmt->bindValue(':inherited_role_id', $inheritedRoleId);
        }
        $stmt->execute();
    }

    // Update specific roles
    protected function updateSpecificRoles($roleId, $specificRoles){
        $this->deleteSpecificRoles($roleId);
        $this->insertSpecificRoles($roleId, $specificRoles);
    }

    // Delete existing specific roles
    protected function deleteSpecificRoles($roleId,$specificRoleId=null){
        $query = "DELETE FROM specific_role WHERE role_id = :role_id";
        if (!empty($specificRoleId)) {
            $query .= " AND specific_role_id = :specific_role_id";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        if (!empty($specificRoleId)) {
            $stmt->bindValue(':specific_role_id', $specificRoleId);
        }
        $stmt->execute();
    }
    // Update role resources
    protected function updateRoleResources($roleId, $resources){
        $this->deleteRoleResources($roleId);
        $this->insertRoleResources($roleId, $resources);
    }

    // Delete existing role resources
    protected function deleteRoleResources($roleId){
        $query = "DELETE FROM role_resource WHERE role_id = :role_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':role_id', $roleId);
        $stmt->execute();
    }
    // Delete a role and your attributes
    public function deleteRole($roleName)
    {
        $existingRoleId = $this->fetchRoleIdByName($roleName);
        if (!$existingRoleId) {
            return false; // Role doesn't exist and is an foreign key
        }
        // Cascade delete from related tables
        $this->deleteRoleResources($existingRoleId);
        $this->deleteInheritedRoles($existingRoleId);
        $this->deleteSpecificRoles($existingRoleId);

        $query = 'DELETE FROM role WHERE name = :name';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $roleName);
        $stmt->execute();
        
    }
}

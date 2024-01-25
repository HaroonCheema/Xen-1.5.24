<?php

class FS_DeleteEmail_Model_User extends XFCP_FS_DeleteEmail_Model_User
{
    
    public function prepareUserOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
    {

            $choices = array(
                    'username' => 'user.username',
                    'register_date' => 'user.register_date',
                    'message_count' => 'user.message_count',
                    'trophy_points' => 'user.trophy_points',
                    'like_count' => 'user.like_count',
                    'last_activity' => 'user.last_activity',
                    'deleted_by' =>  'user.deleted_by',
            );
            return $this->getOrderByClause($choices, $fetchOptions, $defaultOrderSql);
    }
    
    public function prepareUserConditions(array $conditions, array &$fetchOptions)
    {
        $parent = parent::prepareUserConditions($conditions, $fetchOptions);
        
        if (!empty($conditions['deleted_by']) && is_array($conditions['deleted_by']))
        {
                $sqlConditions[] = $this->getCutOffCondition("user.deleted_by", $conditions['deleted_by']);
                return $this->getConditionsForClause($sqlConditions);
        }
                
        return $parent;
    }

}

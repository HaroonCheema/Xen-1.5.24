<?php

class FS_ThreadCount_Model_User extends XFCP_FS_ThreadCount_Model_User
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
                    'thread_count' =>  'user.thread_count',
            );
            return $this->getOrderByClause($choices, $fetchOptions, $defaultOrderSql);
    }
    
    public function prepareUserConditions(array $conditions, array &$fetchOptions)
    {
        $parent = parent::prepareUserConditions($conditions, $fetchOptions);
        
        if (!empty($conditions['thread_count']) && is_array($conditions['thread_count']))
        {
                $sqlConditions[] = $this->getCutOffCondition("user.thread_count", $conditions['thread_count']);
                return $this->getConditionsForClause($sqlConditions);
        }
                
        return $parent;
    }

}

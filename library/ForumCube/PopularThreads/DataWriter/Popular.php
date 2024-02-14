<?php
class ForumCube_PopularThreads_DataWriter_Popular extends XenForo_DataWriter
{
 
    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'fc_thread_view' => array(
                'id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'date' => array('type' => self::TYPE_UINT),
                'thread_id' =>  array('type' => self::TYPE_UINT),
                'view_count' =>  array('type' => self::TYPE_UINT),    
       
            )
        );
    }

    /**
     * Gets the actual existing data out of data that was passed in. This data
     * may be a scalar or an array. If it's a scalar, assume that it is the primary
     * key (if there is one); if it is an array, attempt to extract the primary key
     * (or some other unique identifier). Then fetch the correct data from a model.
     *
     * @param mixed Data that can uniquely ID this item
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data, 'id'))
        {
            return false;
        }

        return array('fc_thread_view' => $this->_getViewsModel()->getViewsById($id));
    }

    /**
     * Gets SQL condition to update the existing record. Should read from {@link _existingData}.
     *
     * @param string Table name
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'id = ' . $this->_db->quote($this->getExisting('id'));
    }

    /**
     * @return ForumCube_PerfectPokemon_Model_Season
     */
    
    public function deleteViews($id)
    {
        return $this->_db->delete('fc_popular_id', "id = $id");
    }


    protected function _getViewsModel()
    {
        return $this->getModelFromCache('ForumCube_PopularThreads_Model_Popular');
    }

}
<?php
class Emja_TaxRelief_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ALLOW_GROUPS = 'emja_taxrelief/settings/allowgroups';
    const XML_PATH_GROUPS_LIST = 'emja_taxrelief/settings/specific_groups';

    /**
     * Check if allowed for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    public function allowedForCustomer($customer)
    {
        if (!$this->getAllowSpecificGroups()) {
            return true;
        }
        if (in_array($customer->getGroupId(), $this->getAllowedGroups())) {
            return true;
        }
        return false;
    }

    /**
     * Get allowed customer groups
     *
     * @return array
     */
    public function getAllowedGroups()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_GROUPS_LIST));
    }

    /**
     * Check if specific groups option enabled. In other case will be allowed all groups
     *
     * @return bool
     */
    public function getAllowSpecificGroups()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALLOW_GROUPS);
    }
}
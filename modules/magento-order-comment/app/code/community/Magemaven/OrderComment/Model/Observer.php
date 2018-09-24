<?php
/**
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Magemaven
 * @package     Magemaven_OrderComment
 * @copyright   Copyright (c) 2011-2012 Sergey Storchay <r8@r8.com.ua>
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Magemaven_OrderComment_Model_Observer extends Varien_Object
{
    /**
     * Current comment
     *
     * @var bool|string
     */
    protected $_currentComment = false;

    /**
     * Save comment from agreement form to order
     *
     * @param $observer
     */
    public function saveOrderComment($observer)
    {
        $orderComment = Mage::app()->getRequest()->getPost('ordercomment');
        $comment = '';
        if (is_array($orderComment) && isset($orderComment['comment1'])) {
            $comment1 = trim($orderComment['comment1']);
            $comment1 = 'Additional Comment: '.nl2br(Mage::helper('ordercomment')->stripTags($comment1));
            $comment .= $comment1;

        }
        if (is_array($orderComment) && isset($orderComment['comment2'])) {
            $comment2 = trim($orderComment['comment2']);
            $comment2 = 'Reference Purchase Order: '.nl2br(Mage::helper('ordercomment')->stripTags($comment2));
            $comment .= '<br/>'.$comment2;
        }
        if (!empty($comment)) {
            $order = $observer->getEvent()->getOrder(); 
            $order->setCustomerNoteNotify(true);
            $order->setCustomerNote($comment);
            $this->_currentComment1 = $comment;
        }
    }

    /**
     * Show customer comment in 'My Account'
     *
     * @param $observer
     */
    public function setOrderCommentVisibility($observer)
    {
        if ($this->_currentComment) {
            $statusHistory = $observer->getEvent()->getStatusHistory();

            if ($statusHistory && $this->_currentComment == $statusHistory->getComment()) {
                $statusHistory->setIsVisibleOnFront(1);
            }
        }
    }
}

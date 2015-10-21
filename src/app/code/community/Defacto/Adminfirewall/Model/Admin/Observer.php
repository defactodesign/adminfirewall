<?php

/**
 * Admin Firewall checks
 *
 * @category    Defacto
 * @package     Defacto_Adminfirewall
 * @author      De Facto Design <developers@de-facto.com>
 * @license     GPL-3.0
 */
class Defacto_Adminfirewall_Model_Admin_Observer extends Mage_Admin_Model_Observer
{
    const XML_ADMIN_FIREWALL_EMAIL_TEMPLATE      = 'admin/defacto_adminfirewall/email_template';
    const XML_ADMIN_FIREWALL_TO_EMAIL_IDENTITY   = 'admin/defacto_adminfirewall/to_email_identity';
    const XML_ADMIN_FIREWALL_FROM_EMAIL_IDENTITY = 'admin/defacto_adminfirewall/from_email_identity';

    /**
     * Verify admin access
     *
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function actionPreDispatchAdmin($observer)
    {
        $helper = Mage::helper('defacto_adminfirewall');

        if ($helper->isAdminFirewallEnabled()) {

            $ip = Mage::helper('core/http')->getRemoteAddr();

            if (!$helper->isAccessPermitted($ip)){

                $observer->getEvent()
                         ->getControllerAction()
                         ->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

                $requestInfo = new Varien_Object($helper->getRequestInfo());

                Mage::dispatchEvent('adminfirewall_connection_refused', array(
                    'request_info' => $requestInfo
                ));

                if ($helper->isEmailAlertsEnabled()) {
                    $this->sendEmailAlert($requestInfo->toArray());
                }

                $response = Mage::app()->getResponse();

                if (!$response->getBody(true)) {
                    $response->setHttpResponseCode(403);
                    $response->setBody("<html>
                        <head>
                            <title>403 - Forbidden</title>
                        </head>
                        <body>
                            <h1>Forbidden</h1>
                            <p>You do not have access to view this resource</p>
                        </body>
                    </html>");
                }

                return;
            }
        }

        return parent::actionPreDispatchAdmin($observer);
    }

    /**
     * Send email alert
     *
     * @param  array $emailVariables
     * @return null
     */
    protected function sendEmailAlert(array $emailVariables)
    {
        try{
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $senderId = Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_FROM_EMAIL_IDENTITY);
            $recipientId = Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_TO_EMAIL_IDENTITY);

            $email = Mage::getModel('core/email_template');
            $email->sendTransactional(
                Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_EMAIL_TEMPLATE),
                array(
                    'name'  => Mage::getStoreConfig("trans_email/ident_$senderId/name"),
                    'email' => Mage::getStoreConfig("trans_email/ident_$senderId/email")
                ),
                array(Mage::getStoreConfig("trans_email/ident_$recipientId/email")),
                array(Mage::getStoreConfig("trans_email/ident_$recipientId/name")),
                $emailVariables
            );

            $translate->setTranslateInline(true);
        } catch(Exception $e) {
            Mage::log("Could not send administration firewall email alert", Zend_Log::WARN);
            Mage::log($e->getMessage(), Zend_Log::WARN);
        }
    }
}

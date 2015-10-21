<?php

/**
 * Admin Firewall checks
 *
 * @category    Defacto
 * @package     Defacto_Adminfirewall
 * @author      De Facto Design <developers@de-facto.com>
 * @license     GPL-3.0
 */
class Defacto_Adminfirewall_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_ADMIN_FIREWALL_ENABLED             = 'admin/defacto_adminfirewall/enabled';
    const XML_ADMIN_FIREWALL_WHITELIST           = 'admin/defacto_adminfirewall/whitelist';
    const XML_ADMIN_FIREWALL_EMAIL_ALERTS        = 'admin/defacto_adminfirewall/email_alerts';

    /**
     * Is the admin firewall enabled
     *
     * @return boolean
     */
    public function isAdminFirewallEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_ENABLED);
    }

    /**
     * Get array of whitelisted IP addresses
     *
     * @return array()
     */
    public function getAdminFirewallWhitelist()
    {
        $ips = explode("\n", Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_WHITELIST));
        $ips = array_map('trim', $ips);
        return array_filter($ips);
    }

    /**
     * Is the IP address allowed through the firewall
     *
     * @return boolean
     **/
    public function isAccessPermitted($ip)
    {
        return in_array($ip, $this->getAdminFirewallWhitelist(), true);
    }

    /**
     * Are email notifications enabled
     *
     * @return boolean
     **/
    public function isEmailAlertsEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_ADMIN_FIREWALL_EMAIL_ALERTS);
    }

    /**
     * Get publicly identifiable request information
     *
     * @return array()
     **/
    public function getRequestInfo()
    {
        $http = Mage::helper('core/http');

        return array(
            'ip'         => $http->getRemoteAddr(),
            'time'       => Mage::getModel('core/date')->date('r'),
            'method'     => $_SERVER['REQUEST_METHOD'],
            'host'       => $http->getHttpHost(),
            'request'    => $http->getRequestUri(),
            'user_agent' => $http->getHttpUserAgent(),
            'referrer'   => $http->getHttpReferer(),
        );
    }
}

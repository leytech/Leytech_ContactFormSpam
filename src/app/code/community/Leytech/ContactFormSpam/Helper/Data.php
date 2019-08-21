<?php
/**
 * @package    Leytech_ContactFormSpam
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2019 Leytech
 * @license    https://opensource.org/licenses/MIT  The MIT License  (MIT)
 */

class Leytech_ContactFormSpam_Helper_Data extends Mage_Core_Helper_Data
{
    const XML_PATH_ENABLED = 'leytech_contact_form_spam/settings/enabled';
    const XML_PATH_URL_REJECT_ENABLED = 'leytech_contact_form_spam/settings/url_reject_enabled';
    const XML_PATH_URL_WHITELIST = 'leytech_contact_form_spam/settings/url_whitelist';
    const XML_PATH_WORD_REJECT_ENABLED = 'leytech_contact_form_spam/settings/word_reject_enabled';
    const XML_PATH_WORD_BLACKLIST = 'leytech_contact_form_spam/settings/word_blacklist';

    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function isUrlRejectEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_URL_REJECT_ENABLED);
    }

    public function getUrlWhitelist()
    {
        return Mage::getStoreConfig(self::XML_PATH_URL_WHITELIST);
    }

    public function getUrlWhitelistAsArray()
    {
        return $this->convertListToArray($this->getUrlWhitelist());
    }

    public function isWordRejectEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_WORD_REJECT_ENABLED);
    }

    public function getWordBlacklist()
    {
        return Mage::getStoreConfig(self::XML_PATH_WORD_BLACKLIST);
    }

    public function getWordBlacklistAsArray()
    {
        return $this->convertListToArray($this->getWordBlacklist());
    }

    private function convertListToArray($list)
    {
        return preg_split("/\r\n|\n|\r/", $list);
    }

}

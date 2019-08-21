<?php
/**
 * @package    Leytech_ContactFormSpam
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2019 Leytech
 * @license    https://opensource.org/licenses/MIT  The MIT License  (MIT)
 */

class Leytech_ContactFormSpam_Model_Observer
{
    /** @var Leytech_ContactFormSpam_Helper_Data */
    private $_helper;

    /** @var array */
    private $url_whitelist;
    private $word_blacklist;

    public function __construct()
    {
        $this->_helper = Mage::helper('leytech_contact_form_spam');
        $this->url_whitelist = $this->_helper->getUrlWhitelistAsArray();
        $this->word_blacklist = $this->_helper->getWordBlacklistAsArray();
    }

    /**
     * Run the validation on the pre dispatch observer for the contacts_index_post controller action
     *
     * @param Varien_Event_Observer $observer The dispatched observer
     * @return Mage_Core_Controller_Front_Action
     */
    public function validateContactForm(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getControllerAction();

        if (!$this->_helper->isEnabled()) {
            return $controller;
        }

        $post = Mage::app()->getRequest()->getPost();

        if ($this->_helper->isUrlRejectEnabled() && $this->doesTextContainRejectedURLs($post['comment'])) {
            return $this->redirectWithMessage($controller, 'Links are not permitted in contact form messages.');
        }

        if ($this->_helper->isWordRejectEnabled() && $this->doesTextContainRejectedWords($post['comment'])) {
            return $this->redirectWithMessage($controller, 'The message contains words that are not permitted.');
        }

        return $controller;
    }

    private function doesTextContainRejectedURLs($text)
    {
        // Probably not perfect but it's pretty good
        // From @imme_emosol ref https://mathiasbynens.be/demo/url-regex
        $regex_url = "@(https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?@i";

        if (!preg_match_all($regex_url, $text, $url_matches)) {
            return false;
        }
        foreach ($url_matches[0] as $url) {
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$this->isDomainAllowed($domain)) {
                return true;
            }
        }
        return false;
    }

    private function doesTextContainRejectedWords($text)
    {
        foreach ($this->word_blacklist as $word) {
            if (preg_match('/\b'.$word.'\b/i', $text)) {
                return true;
            }
        }
        return false;
    }

    private function isDomainAllowed($domain)
    {
        if (in_array($domain, $this->url_whitelist)) {
            return true;
        }
        return false;
    }

    private function redirectWithMessage($controller, $message)
    {
        Mage::getSingleton('customer/session')->addError(
            Mage::helper('leytech_contact_form_spam')->__('Message not sent. ' . $message)
        );

        $flag = Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH;
        $controller->getRequest()->setDispatched(true);
        $controller->setFlag('', $flag, true);
        $controller->getResponse()->setRedirect('/contacts');
        return $controller;
    }
}

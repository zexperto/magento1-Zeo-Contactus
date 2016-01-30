<?php
class Zeo_Contactus_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	 $this->loadLayout ();
		$this->getLayout ()->getBlock ( 'contactus_index' )->setFormAction ( Mage::getUrl ( '*/*/post' ) );
		
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );
		$this->renderLayout ();
	  
    }
    public function postAction() {
    	$post = $this->getRequest ()->getPost ();
    	if ($post) {
    		$translate = Mage::getSingleton ( 'core/translate' );
    		/* @var $translate Mage_Core_Model_Translate */
    		$translate->setTranslateInline ( false );
    		try {
    			$postObject = new Varien_Object ();
    			$postObject->setData ( $post );
    
    			$error = false;
    			$message="";
    			if (! Zend_Validate::is ( trim ( $post ['name'] ), 'NotEmpty' )) {
    				$error = true;
    				$message=$this->__("Name can not be empty");
    			}
    
    
    			if (! Zend_Validate::is ( trim ( $post ['email'] ), 'EmailAddress' ) && !$error) {
    				$error = true;
    				$message=$this->__("Email is not correct");
    			}
    			if (! Zend_Validate::is ( trim ( $post ['message'] ), 'NotEmpty' ) && !$error) {
    				$error = true;
    				$message=$this->__("Message can not be empty");
    			}
    			if (Zend_Validate::is ( trim ( $post ['hideit'] ), 'NotEmpty' ) && !$error) {
    				$error = true;
    			}
    
    			if ($error) {
    				throw new Exception ($message);
    			}
    				
    			
    			$email_template_code="zeo_contactus_template";
    			// set your config here
    			$sender_name=	"Sender Name";
    			$sender_email=	"william.hiko@itm-development.com";
    				
    			$recipient_name= "Recipient Name";
    			$recipient_email=	"william.hiko@itm-development.com";
    				
    			$sender = array(
    					'name' => $sender_name,
    					'email' => $sender_email
    			);
    			$recipients = [
    					$recipient_email => $recipient_name
    			];
    			// end config
    
    			$mailTemplate = Mage::getModel ( 'core/email_template' );
    			/* @var $mailTemplate Mage_Core_Model_Email_Template */
    			$mailTemplate->setDesignConfig ( array (
    					'area' => 'frontend'
    			) )->setReplyTo ( $post ['email'] )->sendTransactional (
    					$email_template_code,
    					$sender,
    					array_keys($recipients),
    					array_values($recipients), array (
    							'data' => $postObject
    					) );
    
    			//var_dump($mailTemplate->getProcessedTemplate());exit;
    			if (! $mailTemplate->getSentSuccess ()) {
    				throw new Exception ();
    			}
    
    			$translate->setTranslateInline ( true );
    
    			Mage::getSingleton ( 'customer/session' )->addSuccess ( Mage::helper ( 'contacts' )->__ ( 'Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.' ) );
    			$this->_redirect ( '*/*/' );
    
    			return;
    		} catch ( Exception $e ) {
    			$translate->setTranslateInline ( true );
      			Mage::getSingleton ( 'customer/session' )->addError ( $e->getMessage());
    			$this->_redirect ( '*/*/' );
    			return;
    		}
    	} else {
    		$this->_redirect ( '*/*/' );
    	}
    }
}
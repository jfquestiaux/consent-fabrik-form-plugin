<?php
/**
 * Consent request plugin for Fabrik forms
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.form.acymailing
 * @copyright   Copyright (C) 2005-2018  Better Web - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');

 use \Joomla\CMS\Date\Date;
 
// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-form.php';

/**
 * Consent request plugin for Fabrik forms
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.form.gdpr
 * @since       3.8
 */

class PlgFabrik_FormConsent extends PlgFabrik_Form
{
	protected $html = null;

	/**
	 * Set up the html to be injected into the bottom of the form
	 *
	 * @return  void
	 */

	public function getBottomContent()
	{
		$params    = $this->getParams();
		$formModel = $this->getModel();

		if ($params->get('consent_terms', true))
		{
			$layout = $this->getLayout('form');
			$layoutData = new stdClass;

			$errors = $formModel->getErrors();

			if (array_key_exists('consent_required', $errors))
			{
				$layoutData->errClass = '';
			}
			else
			{
				$layoutData->errClass = 'fabrikHide';
			}

			$layoutData->errText 	   = FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT');
			$layoutData->useFieldset   = $params->get('consent_fieldset', '0') === '1';
			$layoutData->fieldsetClass = $params->get('consent_fieldset_class', '');
			$layoutData->legendClass   = $params->get('consent_legend_class', '');
			$layoutData->legendText    = FText::_($params->get('consent_legend', ''));
			$layoutData->showConsent   = $params->get('consent_terms', '0') === '1';
			$layoutData->consentIntro  = FText::_($params->get('consent_intro_terms'));
			$layoutData->consentText   = FText::_($params->get('consent_terms_text'));
			$this->html 			   = $layout->render($layoutData);
		}
		else
		{
			$this->html = '';
		}

		$opts = new \StdClass();
		$opts->renderOrder = $this->renderOrder;
		$opts->formid  = $formModel->getId();
		$opts = json_encode($opts);

		$this->formJavascriptClass($params, $formModel);
		$formModel->formPluginJS['Consent' . $this->renderOrder] = 'var consent = new Consent(' . $opts . ');';

	}

	/**
	 * Inject custom html into the bottom of the form
	 *
	 * @param   int  $c  Plugin counter
	 *
	 * @return  string  html
	 */

	public function getBottomContent_result($c)
	{
		return $this->html;
	}

	/**
	 * Run right before the form processing
	 * keeps the data to be processed or sent if consent is not given
	 *
	 * @return	bool
	 */
	
	public function onBeforeProcess()
	{
		$formModel = $this->getModel();
		
		if(!array_key_exists('fabrik_contact_consent', $formModel->formData) && $formModel->isNewRecord())
		{
			$formModel->errors['consent_required'] = array(FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT'));
			$formModel->formErrorMsg = FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT');
			return false;
		}
		elseif(!array_key_exists('fabrik_contact_consent', $formModel->formData))
		{
			$formModel->errors['consent_required'] = array(FText::_('PLG_FORM_CONSENT_REMOVE_CONSENT'));
			$formModel->formErrorMsg = FText::_('YYY');
			return false;
		}
	 }
	
	/**
	 * Run right at the end of the form processing
	 * form needs to be set to record in database for this to hook to be called
	 *
	 * @return	bool
	 */

	public function onAfterProcess()
	{
		$params    = $this->getParams(); 
		$formModel = $this->getModel();
		$data 	   = $this->getProcessData();
		$filter    = JFilterInput::getInstance();
		$post      = $filter->clean($_POST, 'array');
		$contact   = array_key_exists('fabrik_contact_consent', $post);
		
		if($params->get('consent_juser', '0') === '1')
		{
			$userIdField = $this->getFieldName('consent_field_userid');
			$userId = $data[$userIdField];
		}

		// If consent is missing for contact, do nothing
		if ($formModel->isNewRecord() && !$contact)
		{
			return;
		}
		
		// Record consent
		// To be valid a consent must record the date/time of the consent, the identity of the user and the consent message he agreed to.
		// If you edit a user's data, you must keep a record of the change
		if($formModel->isNewRecord() || $params->get('consent_juser', '0') === '1')
		{
			$now 	   = new JDate('now');
			$listId	   = $data['listid'];
			$formId	   = $data['formid'];
			$rowId	   = $data['rowid'];
			
			$consentMessage = $params->get('consent_terms_text');
			   
			// Flag the record when user's data are updated
			if($formModel->isNewRecord())
			{
				$update = '0';
			}
			else
			{
				$update = '1';
			}
		
			$db    	 = JFactory::getDBO();
			$query 	 = $db->getQuery( true );
			$columns = array('id', 'date_time', 'list_id', 'form_id', 'row_id', 'user_id', 'consent_message', 'update_record','ip');
			$values  = array('NULL', $db->quote($now->format('Y-m-d H:i:s')), $listId, $formId, $rowId, $userId, $db->quote($consentMessage), $update, $db->quote($_SERVER['REMOTE_ADDR']));
			$query->insert($db->quoteName('#__fabrik_privacy'))
				  ->columns($db->quoteName($columns))
				  ->values(implode(',', $values));
			$db->setQuery($query); 
			$db->execute();
		}
	}
}

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

		if ($params->get('consent_contact', true))
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

			$layoutData->errText = FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT');
			$layoutData->useFieldset = $params->get('consent_fieldset', '0') === '1';
			$layoutData->fieldsetClass = $params->get('consent_fieldset_class', '');
			$layoutData->legendClass = $params->get('consent_legend_class', '');
			$layoutData->legendText = FText::_($params->get('consent_legend', ''));
			$layoutData->showConsent = $params->get('consent_contact', '0') === '1';
			$layoutData->consentText = FText::_($params->get('consent_consent_text'));
			$this->html = $layout->render($layoutData);
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
		
		if(!array_key_exists('fabrik_contact_consent', $formModel->formData))
		{
			$formModel->errors['consent_required'] = array(FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT'));
			$formModel->formErrorMsg = FText::_('PLG_FORM_CONSENT_PLEASE_CONFIRM_CONSENT');
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
		
		// Record consent
		// If consent is missing for contact and newsletter, do nothing
		if ($formModel->isNewRecord() && !$contact)
		{
			return;
		}
		
		// When editing a record, don't process consent again
		if($formModel->isNewRecord())
		{
			$now 	   = new JDate('now');
			$reference = $data['listid'] . '.' . $data['formid'] . '.' . $data['rowid'];
			$listId	   = $data['listid'];
			
			if($contact)
			{
				$contactId 	    = $data['rowid'];
				$contactMessage = $params->get('consent_consent_text');
			}
		
		$db    	 = JFactory::getDBO();
		$query 	 = $db->getQuery( true );
		$columns = array('id', 'date_time', 'reference', 'list_id', 'contact_id', 'contact_message', 'ip');
		$values  = array('NULL', $db->quote($now->format('Y-m-d H:i:s')), $db->quote($reference), $listId, $db->quote($contactId), $db->quote($contactMessage), $db->quote($_SERVER['REMOTE_ADDR']));
		$query->insert($db->quoteName('#__fabrik_privacy'))
			  ->columns($db->quoteName($columns))
			  ->values(implode(',', $values));
		$db->setQuery($query);
		$db->execute();
		}
	}
	
}

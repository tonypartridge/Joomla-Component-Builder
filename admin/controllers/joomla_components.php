<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		@update number 338 of this MVC
	@build			20th May, 2017
	@created		6th May, 2015
	@package		Component Builder
	@subpackage		joomla_components.php
	@author			Llewellyn van der Merwe <http://vdm.bz/component-builder>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	Builds Complex Joomla Components 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Joomla_components Controller
 */
class ComponentbuilderControllerJoomla_components extends JControllerAdmin
{
	protected $text_prefix = 'COM_COMPONENTBUILDER_JOOMLA_COMPONENTS';
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Joomla_component', $prefix = 'ComponentbuilderModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_component.export', 'com_componentbuilder') && $user->authorise('core.export', 'com_componentbuilder'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Joomla_components');
			// get the data to export
			$data = $model->getExportData($pks);
			if (ComponentbuilderHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				ComponentbuilderHelper::xls($data,'Joomla_components_'.$date->format('jS_F_Y'),'Joomla components exported ('.$date->format('jS F, Y').')','joomla components');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_COMPONENTBUILDER_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_component.import', 'com_componentbuilder') && $user->authorise('core.import', 'com_componentbuilder'))
		{
			// Get the import model
			$model = $this->getModel('Joomla_components');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ComponentbuilderHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('joomla_component_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'joomla_components');
				$session->set('dataType_VDM_IMPORTINTO', 'joomla_component');
				// Redirect to import view.
				$message = JText::_('COM_COMPONENTBUILDER_IMPORT_SELECT_FILE_FOR_JOOMLA_COMPONENTS');
				$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=import_joomla_components', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_COMPONENTBUILDER_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), $message, 'error');
		return;
	}  

	public function smartImport()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_component.import', 'com_componentbuilder') && $user->authorise('core.import', 'com_componentbuilder'))
		{
			$session = JFactory::getSession();
			$session->set('backto_VDM_IMPORT', 'joomla_components');
			$session->set('dataType_VDM_IMPORTINTO', 'smart_package');
			// Redirect to import view.
			$message = JText::_('COM_COMPONENTBUILDER_YOU_CAN_NOW_SELECT_THE_COMPONENT_BZIPB_PACKAGE_YOU_WOULD_LIKE_TO_IMPORTBR_SMALLPLEASE_NOTE_THAT_SMART_COMPONENT_IMPORT_ONLY_WORKS_WITH_THE_FOLLOWING_FORMAT_BZIPBSMALL');
			$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=import_joomla_components&target=smartPackage', false), $message);
			return;
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_COMPONENTBUILDER_YOU_DO_NOT_HAVE_PERMISSION_TO_IMPORT_A_COMPONENT_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR_FOR_MORE_HELP');
		$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), $message, 'error');
		return;
	}  

        public function smartExport()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('joomla_component.export', 'com_componentbuilder') && $user->authorise('core.export', 'com_componentbuilder'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// check if there is any selections
			if (!ComponentbuilderHelper::checkArray($pks))
			{
				// Redirect to the list screen with error.
				$message = JText::_('COM_COMPONENTBUILDER_NO_COMPONENTS_WERE_SELECTED_PLEASE_MAKE_A_SELECTION_AND_TRY_AGAIN');
				$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), $message, 'error');
				return;
			}
			// Get the model
			$model = $this->getModel('Joomla_components');
			// set auto loader
			ComponentbuilderHelper::autoLoader('smart');
			// get the data to export
			if ($model->getSmartExport($pks))
			{
				// set the key string
				if (componentbuilderHelper::checkString($model->key) && strlen($model->key) == 32)
				{
					$keyNotice = '<h1>' . JText::sprintf('COM_COMPONENTBUILDER_THE_PACKAGE_KEY_IS_CODESCODE', $model->key) . '</h1>';
					$keyNotice .= '<p>' . JText::_('COM_COMPONENTBUILDER_YOUR_DATA_IS_ENCRYPTED_WITH_A_AES_ONE_HUNDRED_AND_TWENTY_EIGHT_BIT_ENCRYPTION_USING_THE_ABOVE_THIRTY_TWO_CHARACTER_KEYBR_WITHOUT_THIS_KEY_IT_WILL_TAKE_THE_CURRENT_TECHNOLOGY_WITH_A_BRUTE_FORCE_ATTACK_METHOD_MORE_THEN_A_HREFHTTPRANDOMIZECOMHOWLONGTOHACKPASS_TARGET_BLANK_TITLEHOW_LONG_TO_HACK_PASSSEVEN_HUNDRED_ZERO_ZERO_ZERO_ZERO_ZERO_ZERO_ZERO_ZERO_ZERO_ZEROA_YEARS_TO_CRACK_THEORETICALLY') . '</h1>';
					// set the package owner info
					if ((isset($model->info['getKeyFrom']['company']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['company'])) || (isset($model->info['getKeyFrom']['owner']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['owner'])))
					{
						$ownerDetails = '<h2>' . JText::_('COM_COMPONENTBUILDER_PACKAGE_OWNER_DETAILS') . '</h2>';
						$ownerDetails .= '<ul>';
						if (isset($model->info['getKeyFrom']['company']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['company']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMCOMPANYEM_BSB', $model->info['getKeyFrom']['company']) . '</li>';
						}
						// add value only if set
						if (isset($model->info['getKeyFrom']['owner']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['owner']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMOWNEREM_BSB', $model->info['getKeyFrom']['owner']) . '</li>';
						}
						// add value only if set
						if (isset($model->info['getKeyFrom']['website']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['website']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMWEBSITEEM_BSB', $model->info['getKeyFrom']['website']) . '</li>';
						}
						// add value only if set
						if (isset($model->info['getKeyFrom']['email']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['email']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMEMAILEM_BSB', $model->info['getKeyFrom']['email']) . '</li>';
						}
						// add value only if set
						if (isset($model->info['getKeyFrom']['license']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['license']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMLICENSEEM_BSB', $model->info['getKeyFrom']['license']) . '</li>';
						}
						// add value only if set
						if (isset($model->info['getKeyFrom']['copyright']) && componentbuilderHelper::checkString($model->info['getKeyFrom']['copyright']))
						{
							$ownerDetails .= '<li>' . JText::sprintf('COM_COMPONENTBUILDER_EMCOPYRIGHTEM_BSB', $model->info['getKeyFrom']['copyright']) . '</li>';
						}							
						$ownerDetails .= '</ul>';
					}
					else
					{
						$ownerDetails = '<h2>' . JText::_('COM_COMPONENTBUILDER_PACKAGE_OWNER_NOT_SET') . '</h2>';
						$ownerDetails .= '<p>' . JText::_('COM_COMPONENTBUILDER_TO_CHANGE_THE_PACKAGE_OWNER_DEFAULTS_OPEN_THE_BJCB_GLOBAL_OPTIONSB_GO_TO_THE_BCOMPANYB_TAB_AND_ADD_THE_CORRECT_COMPANY_DETAILS_THERE') . '</p>';
						$ownerDetails .= '<h3>' . JText::_('COM_COMPONENTBUILDER_YOU_SHOULD_ADD_THE_CORRECT_OWNER_DETAILS') . '</h3>';
						$ownerDetails .= '<p>' . JText::_('COM_COMPONENTBUILDER_SINCE_THE_OWNER_DETAILS_ARE_DISPLAYED_DURING_BIMPORT_PROCESSB_BEFORE_ADDING_THE_KEY_THIS_WAY_IF_THE_USERDEV_BDOES_NOTB_HAVE_THE_KEY_THEY_CAN_SEE_BWHERE_TO_GET_ITB') . '</p>';
					}
				}
				else
				{
					$keyNotice = '<h1>' . JText::_('COM_COMPONENTBUILDER_THIS_PACKAGE_HAS_NO_KEY') . '</h1>';
					$ownerDetails = '<p>' . JText::_('COM_COMPONENTBUILDER_THAT_MEANS_ANYONE_WHO_HAS_THIS_PACKAGE_CAN_INSTALL_IT_INTO_JCB_TO_ADD_AN_EXPORT_KEY_SIMPLY_OPEN_THE_COMPONENT_GO_TO_THE_TAB_CALLED_BSETTINGSB_BOTTOM_RIGHT_THERE_IS_A_FIELD_CALLED_BEXPORT_KEYB') . '</p>';
				}
				// Redirect to the list screen with success.
				$message = array();
				$message[] = '<h1>' . JText::_('COM_COMPONENTBUILDER_EXPORT_COMPLETED') . '</h1>';
				$message[] = '<p>' . JText::sprintf('COM_COMPONENTBUILDER_PATH_TO_THE_ZIPPED_PACKAGE_IS_CODESCODE_BR_S_S', $model->zipPath, $keyNotice, $ownerDetails) . '</p>';
				$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), implode('', $message), 'Success');
				return;
			}
			else
			{
				if (componentbuilderHelper::checkString($model->packagePath))
				{
					// clear all if not successful
					ComponentbuilderHelper::removeFolder($model->packagePath);
				}
				if (componentbuilderHelper::checkString($model->zipPath))
				{
					// clear all if not successful
					JFile::delete($model->zipPath);
				}				
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_COMPONENTBUILDER_EXPORT_FAILED_PLEASE_TRY_AGAIN_LATTER');
		$this->setRedirect(JRoute::_('index.php?option=com_componentbuilder&view=joomla_components', false), $message, 'error');
		return;
	}
}

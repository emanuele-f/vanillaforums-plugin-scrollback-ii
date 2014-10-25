<?php
if(!defined('APPLICATION')) exit();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'class.scrollbackiojavascript.php';

$PluginInfo['ScrollbackIO'] = array(
	'Name' => 'Scrollback.io',
	'Description' => 'Adds Scrollback.io JavaScript to forum',
	'Version' => '0.1',
	'MobileFriendly' => TRUE,
	'Author' => 'James Ward',
	'SettingsUrl' => '/dashboard/plugin/scrollbackio'
);

class ScrollbackIOPlugin extends Gdn_Plugin {
	public function Base_Render_Before($Sender) {
		if ($Sender->Application !== 'Vanilla') {
			return;
		}

		$Session = GDN::Session();

		$ConfigurationDirectives = array(
			'UseLightTheme',
			'StartOpen',
			'Room',
			'Host',
			'GuestUsername',
		);

		$ScrollbackIOJavascript = new ScrollbackIOJavascript();

		foreach ($ConfigurationDirectives as $Directive) {
			$ConfigurationValue = C('Plugins.ScrollbackIO.' . $Directive, 'default');

			if ($ConfigurationValue !== 'default') {
				$ScrollbackIOJavascript->{'set' . $Directive}($ConfigurationValue);
			}
		}

		$HideFromGuests = C('Plugins.ScrollbackIO.HideFromGuests');

		if (!empty($Session->User->Name)) {
			$ScrollbackIOJavascript->setUsername($Session->User->Name);
		} else if ($HideFromGuests) {
			return;
		}

		$Sender->Head->AddString(
			$ScrollbackIOJavascript->__toString()
		);
	}

	public function PluginController_ScrollbackIO_Create($Sender) {
		$Sender->Title('Scrollback.IO');
		$Sender->AddSideMenu('plugin/scrollbackio');
		$Sender->Permission('Garden.Settings.Manage');

		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();

		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);

		$ConfigurationModel->SetField(
			array(
				'Plugins.ScrollbackIO.Room',
				'Plugins.ScrollbackIO.UseLightTheme',
				'Plugins.ScrollbackIO.StartOpen',
				'Plugins.ScrollbackIO.Host',
				'Plugins.ScrollbackIO.GuestUsername',
				'Plugins.ScrollbackIO.HideFromGuests',
			)
		);

		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
			$Data = $Sender->Form->FormValues();

			$ConfigurationModel->Validation->ApplyRule('Plugins.ScrollbackIO.StartOpen', 'Boolean', 'Flag to minimize must be true or false.');
			$ConfigurationModel->Validation->ApplyRule('Plugins.ScrollbackIO.UseLightTheme', 'Boolean', 'Flag to use light theme must be true or false.');

			if ($Sender->Form->Save() !== FALSE) {
				$Sender->StatusMessage = T("Your settings have been saved.");
			}
		}

		$Sender->Render($this->GetView('scrollbackio.php'));
	}

	public function Setup(){}
}

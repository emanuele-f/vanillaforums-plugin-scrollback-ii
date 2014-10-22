<?php
if(!defined('APPLICATION')) exit();

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

		if (!empty($Session->User->Name)) {
			$Username = $Session->User->Name;
		} else {
			$Username = null;
		}

		$Sender->Head->AddString($this->getScrollbackJavascript($Username));
	}

	private function getScrollbackJavascript($Username = null) {
		$UseLightTheme = C('Plugins.ScrollbackIO.UseLightTheme') == true;
		$StartOpen = C('Plugins.ScrollbackIO.StartOpen') == true;
		$Room = C('Plugins.ScrollbackIO.Room', 'vanillaforums');
		$Host = C('Plugins.ScrollbackIO.Host');

		if (empty($Host)) {
			$Host = 'scrollback.io';
		}

		$Configuration = [
			'room'     => $Room,
			'form'     => 'toast',
			'theme'    => $UseLightTheme ? 'light' : 'dark',
			'minimize' => $StartOpen
		];

		if (!empty($Username)) {
			$Configuration['nick'] = $Username;
		}

		$ClientJSFile = '//' . $Host . '/client.min.js';

		$JavascriptBody .= 'window.scrollback = ' . json_encode($Configuration) . ';';
		$JavascriptBody .= '(function(d,s,h,e){e=d.createElement(s);e.async=1;e.src="' . $ClientJSFile . '";d.getElementsByTagName(s)[0].parentNode.appendChild(e);}(document,"script"));';

		return '<script type="text/javascript">' . $JavascriptBody . '</script>';
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
				'Plugins.ScrollbackIO.Host'
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

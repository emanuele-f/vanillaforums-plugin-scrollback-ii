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

class ScrollbackIOJavascript {
	const DEFAULT_HOST = 'scrollback.io';
	const DEFAULT_ROOM = 'vanillaforums';

	private $Host = self::DEFAULT_HOST;
	private $Room = self::DEFAULT_ROOM;

	private $UseLightTheme = false;
	private $StartOpen = false;

	private $Username;

	public function setHost($Host) {
		if (empty($Host)) {
			$Host = self::DEFAULT_HOST;
		}

		$this->Host = $Host;
	}

	public function setRoom($Room) {
		if (empty($Room)) {
			$Room = self::DEFAULT_ROOM;
		}

		$this->Room = $Room;
	}

	public function setUseLightTheme($UseLightTheme) {
		$this->UseLightTheme = $UseLightTheme == true;
	}

	public function setStartOpen($StartOpen) {
		$this->StartOpen = $StartOpen == true;
	}

	public function setUsername($Username) {
		$this->Username = $Username;
	}

	public function __toString() {
		$Configuration = array(
			'room'     => $this->Room,
			'form'     => 'toast',
			'theme'    => $this->UseLightTheme ? 'light' : 'dark',
			'minimize' => ! $this->StartOpen
		);

		if (!empty($this->Username)) {
			$Configuration['nick'] = $this->Username;
		}

		$ClientJSFile = '//' . $this->Host . '/client.min.js';

		$JavascriptBody .= 'window.scrollback = ' . json_encode($Configuration) . ';';
		$JavascriptBody .= '(function(d,s,h,e){e=d.createElement(s);e.async=1;e.src="' . $ClientJSFile . '";d.getElementsByTagName(s)[0].parentNode.appendChild(e);}(document,"script"));';

		return '<script type="text/javascript">' . $JavascriptBody . '</script>';
	}
}

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
		);

		$ScrollbackIOJavascript = new ScrollbackIOJavascript();

		foreach ($ConfigurationDirectives as $Directive) {
			$ConfigurationValue = C('Plugins.ScrollbackIO.' . $Directive, 'default');

			if ($ConfigurationValue !== 'default') {
				$ScrollbackIOJavascript->{'set' . $Directive}($ConfigurationValue);
			}
		}

		if (!empty($Session->User->Name)) {
			$ScrollbackIOJavascript->setUsername($Session->User->Name);
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

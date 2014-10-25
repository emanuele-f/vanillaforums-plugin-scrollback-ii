<?php
if(!defined('APPLICATION')) exit();

class ScrollbackIOJavascript {
	const DEFAULT_HOST = 'scrollback.io';
	const DEFAULT_ROOM = 'vanillaforums';

	private $Host = self::DEFAULT_HOST;
	private $Room = self::DEFAULT_ROOM;

	private $UseLightTheme = false;
	private $StartOpen = false;

	private $Username;
	private $GuestUsername;

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

	public function setGuestUsername($GuestUsername) {
		$this->GuestUsername = $GuestUsername;
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
		} else if (!empty($this->GuestUsername)) {
			$Configuration['nick'] = $this->GuestUsername;
		}

		$ClientJSFile = '//' . $this->Host . '/client.min.js';

		$JavascriptBody .= 'window.scrollback = ' . json_encode($Configuration) . ';';
		$JavascriptBody .= '(function(d,s,h,e){e=d.createElement(s);e.async=1;e.src="' . $ClientJSFile . '";d.getElementsByTagName(s)[0].parentNode.appendChild(e);}(document,"script"));';

		return '<script type="text/javascript">' . $JavascriptBody . '</script>';
	}
}

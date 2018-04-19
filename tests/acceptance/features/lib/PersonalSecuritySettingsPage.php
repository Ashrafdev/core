<?php

/**
 * ownCloud
 *
 * @author Artur Neumann <artur@jankaritech.com>
 * @copyright Copyright (c) 2017 Artur Neumann artur@jankaritech.com
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License,
 * as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace Page;

use Behat\Mink\Element\NodeElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;

/**
 * Personal Security Settings page.
 */
class PersonalSecuritySettingsPage extends OwncloudPage {

	/**
	 *
	 * @var string $path
	 */
	protected $path = '/index.php/settings/personal?sectionid=security';

	protected $createNewAppFormId = 'app-password-form';
	protected $appPasswordNameInputId = 'app-password-name';
	protected $createNewAppPasswordButtonId = 'add-app-password';
	protected $linkedAppsTrXpath = './/*[@id="apppasswords"]/table/tbody/tr';
	protected $linkedAppNameXpath = '//span[@class="token-name"]';
	protected $disconnectButtonXpath = '//*[@data-original-title="Disconnect"]';
	protected $createNewAppPasswordLoadingIndicatorClass = 'icon-loading-small';

	/**
	 * create a new app password for the app named $appName
	 *
	 * @param string $appName
	 *
	 * @throws ElementNotFoundException
	 * @return void
	 */
	public function createNewAppPassword($appName) {
		$this->fillField($this->appPasswordNameInputId, $appName);
		$createNewAppPasswordButton = $this->findById(
			$this->createNewAppPasswordButtonId
		);

		if (\is_null($createNewAppPasswordButton)) {
			throw new ElementNotFoundException(
				__METHOD__ .
				" id $this->createNewAppPasswordButtonId " .
				"could not find create new app password button (1)"
			);
		}

		$createNewAppPasswordButton->click();

		$createNewAppPasswordButton = $this->findById(
			$this->createNewAppPasswordButtonId
		);

		if (\is_null($createNewAppPasswordButton)) {
			throw new ElementNotFoundException(
				__METHOD__ .
				" id $this->createNewAppPasswordButtonId " .
				"could not find create new app password button (2)"
			);
		}

		while (\strpos(
			$createNewAppPasswordButton->getAttribute("class"),
			$this->createNewAppPasswordLoadingIndicatorClass
		) !== false
		) {
			\usleep(STANDARDSLEEPTIMEMICROSEC);
		}

	}

	/**
	 * finds and returns the TR NodeElement of the app
	 * throws Exception if App could not be found in the list
	 *
	 * @param string $appName
	 *
	 * @throws \Exception
	 * @return NodeElement
	 */
	public function getLinkedAppByName($appName) {
		$appTrs = $this->findAll("xpath", $this->linkedAppsTrXpath);
		foreach ($appTrs as $appTr) {
			$app = $appTr->find("xpath", $this->linkedAppNameXpath);
			if (!\is_null($app) && ($this->getTrimmedText($app) === $appName)) {
				return $appTr;
			}
		}
		throw new \Exception("Could not find app '$appName'");
	}

	/**
	 * Takes a TR NodeElement and looks for the disconnect button in it
	 * returns the NodeElement of the button if found, else NULL
	 *
	 * @param NodeElement $tr
	 *
	 * @return NodeElement|NULL
	 */
	public function getDisconnectButton(NodeElement $tr) {
		return $tr->find("xpath", $this->disconnectButtonXpath);
	}

	/**
	 * finds the result fields of the new app password and
	 * returns an array of [login-name,password]
	 *
	 * @return NodeElement[]|NULL[]
	 */
	public function getAppPasswordResult() {
		return array (
			$this->findField("new-app-login-name"),
			$this->findField("new-app-password")
		);
	}
}
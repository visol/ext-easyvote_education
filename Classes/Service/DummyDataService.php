<?php
namespace Visol\EasyvoteEducation\Service;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Lorenz Ulrich <lorenz.ulrich@visol.ch>, visol digitale Dienstleistungen GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * DummyDataService
 */
class DummyDataService  {

	/**
	 * @param $numberOfNames
	 * @return array
	 */
	public function getRandomNames($numberOfNames) {
		$firstNamesString = LocalizationUtility::translate('voting.actions.new.dummyText.firstNames', 'EasyvoteEducation');
		$firstNames = GeneralUtility::trimExplode(',', $firstNamesString);
		$lastNamesString = LocalizationUtility::translate('voting.actions.new.dummyText.lastNames', 'EasyvoteEducation');
		$lastNames = GeneralUtility::trimExplode(',', $lastNamesString);

		$randomNames = array();
		for ($i = 1; $i <= $numberOfNames; $i++) {
			$firstNameKey = array_rand($firstNames);
			$lastNameKey = array_rand($lastNames);
			$randomNames[] = $firstNames[$firstNameKey] . ' ' . $lastNames[$lastNameKey];
		}

		return $randomNames;

	}

	/**
	 * @param $numberOfColors
	 * @return array
	 */
	public function getRandomColors($numberOfColors) {
		$colorsString = LocalizationUtility::translate('voting.actions.new.dummyText.colors', 'EasyvoteEducation');
		$colors = GeneralUtility::trimExplode(',', $colorsString);

		$randomColors = array();
		for ($i = 1; $i <= $numberOfColors; $i++) {
			$colorKey = array_rand($colors);
			$randomColors[] = $colors[$colorKey];
		}

		return $randomColors;

	}

}
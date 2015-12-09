<?php
namespace Visol\EasyvoteEducation\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DummyDataService
{

    /**
     * @param $numberOfNames
     * @return array
     */
    public function getRandomNames($numberOfNames)
    {
        $firstNamesString = LocalizationUtility::translate('voting.actions.new.dummyText.firstNames',
            'EasyvoteEducation');
        $firstNames = GeneralUtility::trimExplode(',', $firstNamesString);
        $lastNamesString = LocalizationUtility::translate('voting.actions.new.dummyText.lastNames',
            'EasyvoteEducation');
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
    public function getRandomColors($numberOfColors)
    {
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
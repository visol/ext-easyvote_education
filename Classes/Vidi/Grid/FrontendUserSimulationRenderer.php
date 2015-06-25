<?php
namespace Visol\EasyvoteEducation\Vidi\Grid;

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

/**
 * Class rendering a Frontend User simulation button
 */
class FrontendUserSimulationRenderer extends \Fab\Vidi\Grid\ColumnRendererAbstract {

	/**
	 * @var $loginAsObj \Cabag\CabagLoginas\Hook\ToolbarItemHook
	 */
	public $loginAsObj = NULL;

	public function __construct() {
		$this->loginAsObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Cabag\CabagLoginas\Hook\ToolbarItemHook');
	}

	/**
	 * Render a representation of the relation on the GUI.
	 *
	 * @return string
	 */
	public function render() {
		$result = '&nbsp;';

		if (!empty($this->object[$this->fieldName])) {
			$result .= $this->loginAsObj->getLoginAsIconInTable($this->object->getCommunityUser()->toArray());
		}

		return $result;

	}

}

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

use Fab\Vidi\Domain\Model\Content;
use Fab\Vidi\Tca\Tca;

/**
 * Class rendering relation
 */
class PanelInvitationsRenderer extends \Fab\Vidi\Grid\ColumnRendererAbstract {

	/**
	 * Render a representation of the relation on the GUI.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';

		if (!empty($this->object[$this->fieldName])) {
			/** @var $foreignObject Content */
			$output = '';
			foreach ($this->object[$this->fieldName] as $foreignObject) {
				if (!empty($foreignObject['attending_community_user'])) {
					$output =
						$foreignObject->getAttendingCommunityUser()->getFirstName() . ' ' .
						$foreignObject->getAttendingCommunityUser()->getLastName();
					if (!empty($foreignObject->getAttendingCommunityUser()->getParty())) {
						$output .= ' (' . $foreignObject->getAttendingCommunityUser()->getParty()->getTitle() . ')';
					}
				} else {
					$output = 'Offen: ';
					if (!empty($foreignObject['allowed_parties'])) {
						foreach ($foreignObject->getAllowedParties() as $party) {
							$output .= $party->getTitle() . ',';
						}
					}
				}
				$result .= sprintf('<li>%s</li>', $output);
			}
			$result = sprintf('<ul>%s</ul>', $result);
		}


		return $result;
	}

	/**
	 * Return the label field of the foreign table.
	 *
	 * @param string $fieldName
	 * @return string
	 */
	protected function getForeignTableLabelField($fieldName) {

		// Get TCA table service.
		$table = Tca::table($this->object);

		// Compute the label of the foreign table.
		$relationDataType = $table->field($fieldName)->relationDataType();
		return Tca::table($relationDataType)->getLabelField();
	}

}

<?php
namespace Visol\EasyvoteEducation\Domain\Repository;

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

class PanelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	protected $defaultOrderings = array(
		'date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
		'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
	);

	/**
	 * Count all panels that take place in a given kanton
	 * Only consider panels that have panelInvitations
	 * This is used to determine if more panels can be created in the same kanton
	 *
	 * @param \Visol\Easyvote\Domain\Model\Kanton $kanton
	 * @return int
	 */
	public function countPanelsWithInvitationsByKanton(\Visol\Easyvote\Domain\Model\Kanton $kanton) {
		$query = $this->createQuery();
		$query->matching(
			$query->logicalAnd(
				$query->equals('city.kanton', $kanton),
				$query->logicalNot(
					$query->equals('panelInvitations', 0)
				)
			)
		);
		return $query->execute()->count();
	}

}
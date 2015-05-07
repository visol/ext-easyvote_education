<?php
namespace Visol\EasyvoteEducation\Domain\Repository;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * The repository for PanelInvitations
 */
class PanelInvitationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	protected $defaultOrderings = array(
		'panel.date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
	);

	/**
	 * Find panel invitations in the future that need a candidate of a party the current user belongs to. The
	 * panel may not be already ignored or attended by the current user and must take place in the Kanton the current
	 * user resides.
	 *
	 * @param \Visol\Easyvote\Domain\Model\CommunityUser $communityUser
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findFutureNotIgnoredPanelsByCommunityUser(\Visol\Easyvote\Domain\Model\CommunityUser $communityUser) {
		if ($communityUser->getCitySelection() instanceof \Visol\Easyvote\Domain\Model\City && is_object($communityUser->getParty())) {
			// midnigth of current day
			$endOfDay = new \DateTime('23:59:59');
			$endOfDayDate = $endOfDay->format('Y-m-d');

			$query = $this->createQuery();
			$query->matching(
				$query->logicalAnd(
					$query->greaterThanOrEqual('panel.date', $endOfDayDate),
					$query->contains('allowedParties', $communityUser->getParty()),
					$query->logicalNot(
						$query->contains('ignoringCommunityUsers', $communityUser)
					),
					$query->logicalOr(
						$query->equals('attendingCommunityUser', $communityUser),
						$query->equals('attendingCommunityUser', 0)
					),
					$query->equals('panel.city.kanton', $communityUser->getCitySelection()->getKanton())
				)
			);
			return $query->execute();

		} else {
			return NULL;
		}

	}

	/**
	 * Find panels that require politicians of the given party
	 *
	 * @param \Visol\Easyvote\Domain\Model\Party $party
	 * @param null $demand
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByAllowedPartyAndDemand(\Visol\Easyvote\Domain\Model\Party $party, $demand = NULL) {
		$query = $this->createQuery();
		$panelTable = 'tx_easyvoteeducation_domain_model_panel';

		// Used for comparison in query
		$endOfDay = new \DateTime('23:59:59');
		$endOfDayDate = $endOfDay->format('Y-m-d H:i:s');
		$beginningOfDay = new \DateTime('midnight');
		$beginningOfDayDate = $endOfDay->format('Y-m-d H:i:s');

		$constraints = [];
		$constraints[] = $query->contains('allowedParties', $party);

		if (is_array($demand)) {
			if (isset($demand['query'])) {
				// query constraint
				$queryString = '%' . $GLOBALS['TYPO3_DB']->escapeStrForLike($GLOBALS['TYPO3_DB']->quoteStr($demand['query'], $panelTable), $panelTable) . '%';
				$constraints[] = $query->logicalOr(
					$query->like('panel.title', $queryString, FALSE),
					$query->like('panel.city.name', $queryString, FALSE)
				);
			}

			if (isset($demand['kanton']) && (int)$demand['kanton'] > 0) {
				// kanton constraint
				$constraints[] = $query->equals('panel.city.kanton', (int)$demand['kanton']);
			}

			if (isset($demand['status']) && in_array($demand['status'], array('active', 'pending', 'archived'))) {
				if ($demand['status'] === 'active') {
					// Display all future panel invitations
					$constraints[] = $query->greaterThanOrEqual('panel.date', $beginningOfDayDate);
				}
				if ($demand['status'] === 'pending') {
					// Only display future panel invitations without an attending community user
					$constraints[] = $query->logicalAnd(
						$query->equals('attendingCommunityUser', 0),
						$query->greaterThanOrEqual('panel.date', $beginningOfDayDate)
					);
				}
				if ($demand['status'] === 'archived') {
					// Only display past panel invitations
					$constraints[] = $query->lessThanOrEqual('panel.date', $endOfDayDate);
				}
			}
		} else {
			// if no demand is set, don't display archived panels
			$constraints[] = $query->greaterThanOrEqual('panel.date', $beginningOfDayDate);
		}

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute();
	}
	
}
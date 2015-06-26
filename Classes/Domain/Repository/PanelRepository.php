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

	/**
	 * Find all panel in the past that have not been feedback e-mails sent for
	 * The is used in the PanelCommandController
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findPastPanelsWithoutFeedbackEmailSent() {
		$now = new \DateTime();
		$today = $now->format('Y-m-d');

		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching(
			$query->logicalAnd(
				$query->lessThanOrEqual('date', $today),
				$query->equals('feedbackMailSent', FALSE)
			)
		);
		return $query->execute();

	}

	/**
	 * Find panels that happen in a week, two weeks or a month from the actual date
	 * This is used to send a reminder e-mail in the PanelCommandController
	 *
	 * @param $constraint
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findPanelsWithinDateConstraintWithoutReminderEmailSent($constraint) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$constraints = [];

		switch ($constraint) {
			case 'onemonth':
				$nextMonth = new \DateTime('+1 month');
				$todayInAMonth = $nextMonth->format('Y-m-d');
				$constraints[] = $query->equals('date', $todayInAMonth);
				$constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', FALSE);
				break;
			case 'twoweeks':
				$weekAfterNextWeeks = new \DateTime('+2 weeks');
				$todayInTwoWeeks = $weekAfterNextWeeks->format('Y-m-d');
				$constraints[] = $query->equals('date', $todayInTwoWeeks);
				$constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', FALSE);
				break;
			case 'oneweek':
				$nextWeek = new \DateTime('+1 week');
				$todayInAWeek = $nextWeek->format('Y-m-d');
				$constraints[] = $query->equals('date', $todayInAWeek);
				$constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', FALSE);
				break;
		}

		if (count($constraints)) {
			$query->matching(
				$query->logicalAnd(
					$constraints
				)
			);
			return $query->execute();
		}
	}

}
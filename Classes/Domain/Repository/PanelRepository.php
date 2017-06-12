<?php
namespace Visol\EasyvoteEducation\Domain\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Visol\Easyvote\Domain\Model\CommunityUser;

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

class PanelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected $defaultOrderings = array(
        'date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
    );

    /**
     * Count all panels that take place in a given canton
     * Only consider panels that have panelInvitations
     *
     * This is used to determine if more panels can be created in the same canton
     *
     * @param \Visol\EasyvoteEducation\Domain\Model\Panel $panel
     * @return int
     */
    public function countPanelsWithInvitationsByKantonInTimeFrame(\Visol\EasyvoteEducation\Domain\Model\Panel $panel)
    {
        $query = $this->createQuery();
        
        $constraints = [];

        // Panels already having invitations 
        $constraints[] = $query->logicalNot(
            $query->equals('panelInvitations', 0)
        );

        $panelHostingCanton = $panel->getCity()->getKanton();

        $constraints[] = $query->equals('city.kanton', $panelHostingCanton);

        if ($panelHostingCanton->getPanelAllowedFrom() instanceof \DateTime) {
            $constraints[] = $query->greaterThanOrEqual('date', $panelHostingCanton->getPanelAllowedFrom()->format('Y-m-d'));
        }

        if ($panelHostingCanton->getPanelAllowedTo() instanceof \DateTime) {
            $constraints[] = $query->lessThanOrEqual('date', $panelHostingCanton->getPanelAllowedTo()->format('Y-m-d'));
        }
        
        $query->matching(
            $query->logicalAnd(
                $constraints
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
    public function findPastPanelsWithoutFeedbackEmailSent()
    {
        $now = new \DateTime();
        $today = $now->format('Y-m-d');

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->lessThanOrEqual('date', $today),
                $query->equals('feedbackMailSent', false)
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
    public function findPanelsWithinDateConstraintWithoutReminderEmailSent($constraint)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $constraints = [];

        switch ($constraint) {
            case 'onemonth':
                $nextMonth = new \DateTime('+1 month');
                $todayInAMonth = $nextMonth->format('Y-m-d');
                $constraints[] = $query->equals('date', $todayInAMonth);
                $constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', false);
                break;
            case 'twoweeks':
                $weekAfterNextWeeks = new \DateTime('+2 weeks');
                $todayInTwoWeeks = $weekAfterNextWeeks->format('Y-m-d');
                $constraints[] = $query->equals('date', $todayInTwoWeeks);
                $constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', false);
                break;
            case 'oneweek':
                $nextWeek = new \DateTime('+1 week');
                $todayInAWeek = $nextWeek->format('Y-m-d');
                $constraints[] = $query->equals('date', $todayInAWeek);
                $constraints[] = $query->equals('reminder' . ucfirst($constraint) . 'Sent', false);
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

	/**
	 * @param CommunityUser $communityUser
	 * @param bool $respectStoragePage
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByCommunityUser(CommunityUser $communityUser, $respectStoragePage = true) {
		$query = $this->createQuery();
		if (!$respectStoragePage) {
			$query->getQuerySettings()->setRespectStoragePage(false);
		}
		$query->matching(
			$query->equals('communityUser', $communityUser)
		);
		$query->setOrderings([
			'date' => QueryInterface::ORDER_ASCENDING
		]);
		return $query->execute();
	}

}
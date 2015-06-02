<?php
namespace Visol\EasyvoteEducation\Service;
use Visol\EasyvoteEducation\Domain\Model\Panel;

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

class PanelService implements \TYPO3\CMS\Core\SingletonInterface  {

	/**
	 * @var \Visol\Easyvote\Domain\Repository\KantonRepository
	 * @inject
	 */
	protected $kantonRepository = NULL;

	/**
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository = NULL;

	/**
	 * @param Panel $panel
	 * @return boolean
	 */
	public function isPanelInvitationAllowedForPanel(Panel $panel) {
		if (count($panel->getPanelInvitations()) > 0) {
			// If a panel already has invitations, it is allowed to have invitations (quite logical, isn't it :-)?)
			return TRUE;
		}

		$cityOfPanel = $panel->getCity();
		if ($cityOfPanel instanceof \Visol\Easyvote\Domain\Model\City) {
			// panelLimit is either NULL or an integer
			$panelLimitForKanton = $cityOfPanel->getKanton()->getPanelLimit();
			if (is_integer($panelLimitForKanton)) {
				$existingPanelsInKanton = $this->panelRepository->countPanelsWithInvitationsByKanton($panel->getCity()->getKanton());
				if ($existingPanelsInKanton >= $panelLimitForKanton) {
					return FALSE;
				}
			}
		}

		if ($panel->getDate() instanceof \DateTime) {
			$panelTimestamp = $panel->getDate()->getTimestamp();
			$allowedTimestampStart = 1438387200;
			$allowedTimestampEnd = 1445205599;
			if ($panelTimestamp < $allowedTimestampStart || $panelTimestamp > $allowedTimestampEnd) {
				// The panel doesn't take place between August 1, 2015 and October 18, 2015
				return FALSE;
			}

		}

		// return false if all panels are reached or existing panels don't have a city set
		return TRUE;
	}

}
<?php
namespace Visol\EasyvoteEducation\Service;
use Visol\EasyvoteEducation\Domain\Model\Panel;


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

class VotingService  {

	/**
	 * votingRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;

	/**
	 * votingOptionRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingOptionRepository
	 * @inject
	 */
	protected $votingOptionRepository = NULL;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * @param Panel $panel
	 * @return \Visol\EasyvoteEducation\Domain\Model\Voting
	 */
	public function getNextVoting(Panel $panel) {
		if (!$panel->getCurrentVoting() instanceof \Visol\EasyvoteEducation\Domain\Model\Voting) {
			// We have no current state, so we're at the beginning of the panel and need the first voting
			return $this->votingRepository->findFirstVotingByPanel($panel);
		} else {
			return $this->votingRepository->findNextVotingByPanelAndCurrentVoting($panel, $panel->getCurrentVoting());
		}
	}

	/**
	 * @param $currentState
	 * @return \Visol\EasyvoteEducation\Domain\Model\Voting
	 */
	public function getCurrentVoting($currentState) {
		// currentState is in format action/uid
		$currentStateArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('-', $currentState, TRUE);
		if (count($currentStateArray)) {
			return $this->votingRepository->findByUid((int)$currentStateArray[1]);
		} else {
			return NULL;
		}

	}

	/**
	 * @param Panel $panel
	 * @return string
	 */
	public function getViewNameForCurrentPanelState(Panel $panel) {
		// currentState is in format action/uid
		$currentStateArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('-', $panel->getCurrentState(), TRUE);
		if (count($currentStateArray) === 2) {
			return $currentStateArray[0];
		} else {
			return '';
		}
	}

	/**
	 * @param Panel $panel
	 */
	public function processVotingResult(Panel $panel) {
		$voting = $panel->getCurrentVoting();
		$votesCount = 0;
		foreach ($voting->getVotingOptions() as $votingOption) {
			/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
			$votesCountForVotingOption = $votingOption->getVotes()->count();
			$votingOption->setCachedVotes($votesCountForVotingOption);
			$votesCount = $votesCount + $votesCountForVotingOption;
			$this->votingOptionRepository->update($votingOption);
		}
		$this->persistenceManager->persistAll();

		// $votesCount is complete
		foreach ($voting->getVotingOptions() as $votingOption) {
			/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
			$votingResult = round($votingOption->getCachedVotes() / $votesCount, 5) * 100;
			$votingOption->setCachedVotingResult((int)$votingResult);
			$this->votingOptionRepository->update($votingOption);
		}
		$this->persistenceManager->persistAll();
	}

}
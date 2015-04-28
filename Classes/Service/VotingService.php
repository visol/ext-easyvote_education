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

class VotingService implements \TYPO3\CMS\Core\SingletonInterface  {

	/**
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;

	/**
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingOptionRepository
	 * @inject
	 */
	protected $votingOptionRepository = NULL;

	/**
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
	 * @param $votingStepAction
	 * @return string
	 */
	public function getViewNameForCurrentPanelState(Panel $panel, $votingStepAction) {
		// currentState is in format action/uid
		$currentStateArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('-', $panel->getCurrentState(), TRUE);
		if (count($currentStateArray) === 2) {
			return $currentStateArray[0];
		} else {
			// if no currentState is set, keep the passed $votingStepAction
			return $votingStepAction;
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
			if ($votesCount > 0) {
				$votingResult = round($votingOption->getCachedVotes() / $votesCount, 5) * 100;
				$votingOption->setCachedVotingResult((int)$votingResult);
			} else {
				$votingOption->setCachedVotingResult(0);
			}
			$this->votingOptionRepository->update($votingOption);
		}
		$this->persistenceManager->persistAll();
	}

}
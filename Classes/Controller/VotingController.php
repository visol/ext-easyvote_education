<?php
namespace Visol\EasyvoteEducation\Controller;

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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;

class VotingController extends \Visol\EasyvoteEducation\Controller\AbstractController {

	/**
	 * @param Panel $panel
	 * @return string
	 */
	public function listForCurrentUserAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$this->view->assign('panel', $panel);
			return json_encode(array('content' => $this->view->render()));
		} else {
			// Error: Non-owner tries to list votings
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/listForCurrentUserAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * action new
	 *
	 * @param Panel $panel
	 * @param string $selection (1=yesNoAbstention,2=freeText,3=images,4=empty)
	 * @return string
	 */
	public function newAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel, $selection = '') {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			/** @var \Visol\EasyvoteEducation\Domain\Model\Voting $newVoting */
			$newVoting = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\Voting');
			$newVotingTitle = LocalizationUtility::translate('voting.actions.new.dummyText.newVoting', $this->request->getControllerExtensionName());
			$newVoting->setTitle($newVotingTitle);
			$newVoting->setIsVisible(TRUE);
			$newVoting->setVotingDuration(60);
			switch ((int)$selection) {
				case 1:
					// YesNoAbstention
					/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOptionYes */
					$votingOptionYes = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
					$votingOptionDummyText = LocalizationUtility::translate('voting.actions.new.dummyText.yes', $this->request->getControllerExtensionName());
					$votingOptionYes->setTitle($votingOptionDummyText);
					$this->votingOptionRepository->add($votingOptionYes);
					$newVoting->addVotingOption($votingOptionYes);
					/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOptionNo */
					$votingOptionNo = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
					$votingOptionDummyText = LocalizationUtility::translate('voting.actions.new.dummyText.no', $this->request->getControllerExtensionName());
					$votingOptionNo->setTitle($votingOptionDummyText);
					$this->votingOptionRepository->add($votingOptionNo);
					$newVoting->addVotingOption($votingOptionNo);
					/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOptionAbstention */
					$votingOptionAbstention = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
					$votingOptionDummyText = LocalizationUtility::translate('voting.actions.new.dummyText.abstention', $this->request->getControllerExtensionName());
					$votingOptionAbstention->setTitle($votingOptionDummyText);
					$this->votingOptionRepository->add($votingOptionAbstention);
					$newVoting->addVotingOption($votingOptionAbstention);
					break;
				case 2:
					// current unused (commented in Template)
					// Free text
					$randomColors = $this->dummyDataService->getRandomColors(3);
					for ($i = 0; $i < 3; $i++) {
						/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
						$votingOption = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
						$votingOption->setTitle($randomColors[$i]);
						$this->votingOptionRepository->add($votingOption);
						$newVoting->addVotingOption($votingOption);
					}
					break;
				case 3:
					// Text and Images
					$randomNames = $this->dummyDataService->getRandomNames(3);
					for ($i = 0; $i < 3; $i++) {
						/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
						$votingOption = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
						$votingOption->setTitle($randomNames[$i]);
						// todo add dummy images
						$this->votingOptionRepository->add($votingOption);
						$newVoting->addVotingOption($votingOption);
					}
					break;
				default:
					// current unused (commented in Template)
					break;
			}
			$this->votingRepository->add($newVoting);
			$panel->addVoting($newVoting);
			$this->panelRepository->update($panel);
			$this->persistenceManager->persistAll();
			return json_encode(array('reloadVotings' => $panel->getUid()));
		} else {
			// Error: Non-owner tries to create a new Voting
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/newAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * Update a voting
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return string
	 */
	public function updateAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$this->votingRepository->update($voting);
			$this->persistenceManager->persistAll();
			return json_encode(array('status' => 200));
		} else {
			// Error: Non-owner tries to update a Voting
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/updateAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * Delete a voting
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @ignorevalidation $voting
	 * @return string
	 */
	public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$this->votingRepository->remove($voting);
			$this->persistenceManager->persistAll();
			return json_encode(array('removeElement' => TRUE));
		} else {
			// Error: Non-owner tries to delete a Voting
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/deleteAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * Duplicate a voting
	 *
	 * @unused Currently not used and maintained
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return string
	 */
	public function duplicateAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			/** @var \Visol\EasyvoteEducation\Domain\Model\Panel $duplicateVoting */
			$duplicateVoting = $this->cloneService->copy($voting);
			$copyOfText = LocalizationUtility::translate('voting.actions.duplicate.copyOf', $this->request->getControllerExtensionName());
			$duplicateVoting->setTitle($copyOfText . ' ' . $voting->getTitle());
			$this->votingRepository->add($duplicateVoting);
			$voting->getPanel()->addVoting($duplicateVoting);
			$this->persistenceManager->persistAll();
			return json_encode(array('reloadVotings' => $voting->getPanel()->getUid()));
		}  else {
			// Error: Non-owner tries to duplicate a Voting
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/duplicateAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * Sort a voting
	 *
	 * @param Panel $panel
	 * @param array $sorting
	 * @return string
	 */
	public function sortAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel, $sorting) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$votings = $this->votingRepository->findByPanel($panel);
			foreach ($votings as $voting) {
				/** @var $voting \Visol\EasyvoteEducation\Domain\Model\Voting */
				$voting->setSorting((int)$sorting[$voting->getUid()]);
				$this->votingRepository->update($voting);
			}
			$this->persistenceManager->persistAll();
			return json_encode(array('status' => 200));
		} else {
			// Error: Non-owner tries to sort a Voting
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/sortAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}
}
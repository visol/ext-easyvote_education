<?php
namespace Visol\EasyvoteEducation\Controller;


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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;

/**
 * VotingController
 */
class VotingController extends \Visol\EasyvoteEducation\Controller\AbstractController {

	/**
	 * @param Panel $panel
	 * @return string
	 */
	public function listForCurrentUserAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$this->view->assign('panel', $panel);
			return json_encode(array('content' => $this->view->render()));
		}
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$votings = $this->votingRepository->findAll();
		$this->view->assign('votings', $votings);
	}

	/**
	 * action show
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return string
	 */
	public function showAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$this->view->assign('voting', $voting);
			return json_encode(array('content' => $this->view->render()));
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
		/** @var \Visol\EasyvoteEducation\Domain\Model\Voting $newVoting */
		$newVoting = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\Voting');
		$newVotingTitle = LocalizationUtility::translate('voting.actions.new.dummyText.newVoting', $this->request->getControllerExtensionName());
		$newVoting->setTitle($newVotingTitle);
		$newVoting->setIsVisible(TRUE);
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
				break;
		}
		$this->votingRepository->add($newVoting);
		$panel->addVoting($newVoting);
		$this->panelRepository->update($panel);
		$this->persistenceManager->persistAll();
		return json_encode(array('reloadVotings' => $panel->getUid()));
	}

	/**
	 * action edit
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @ignorevalidation $voting
	 * @return string
	 */
	public function editAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$this->view->assign('voting', $voting);
			return json_encode(array('content' => $this->view->render()));
		}
	}

	/**
	 * action update
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
			$reason = LocalizationUtility::translate('ajax.status.403', 'easyvote_education');
			$reason .= '<br />VotingController/updateAction';
			return json_encode(array('status' => 403, 'reason' => $reason));
		}
	}

	/**
	 * action delete
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
		}
	}

	/**
	 * action duplicate
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
		}
	}

	/**
	 * action sort
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
			return json_encode(array('success' => TRUE));
		}
	}
}
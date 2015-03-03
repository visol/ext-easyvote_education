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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;
use Visol\Easyvote\Property\TypeConverter\UploadedFileReferenceConverter;

/**
 * VotingController
 */
class VotingOptionController extends \Visol\EasyvoteEducation\Controller\AbstractController {

	/**
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return string
	 */
	public function listForVotingAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$this->view->assign('voting', $voting);
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
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return string
	 */
	public function newAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		/** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $newVotingOption */
		$newVotingOption = $this->objectManager->get('Visol\EasyvoteEducation\Domain\Model\VotingOption');
		$newVotingOptionTitle = LocalizationUtility::translate('votingOption.actions.new.dummyText.newVotingOption', $this->request->getControllerExtensionName());
		$newVotingOption->setTitle($newVotingOptionTitle);
		$newVotingOption->setSorting(9999);
		$this->votingOptionRepository->add($newVotingOption);
		$voting->addVotingOption($newVotingOption);
		$this->votingRepository->update($voting);
		$this->persistenceManager->persistAll();
		return json_encode(array('reloadVotingOptions' => $voting->getUid()));
	}

	/**
	 * action edit
	 *
	 * @ignorevalidation $voting
	 * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
	 * @return string
	 */
	public function editAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption) {
		if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
			$this->view->assign('votingOption', $votingOption);
			return json_encode(array('content' => $this->view->render()));
		}
	}

	/**
	 * Allow all properties of communityUser
	 * Convert birthdate to DateTime
	 */
	protected function initializeUpdateAction(){
		$propertyMappingConfiguration = $this->arguments['votingOption']->getPropertyMappingConfiguration();
		$uploadConfiguration = array(
			UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
			UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/userimages/',
		);
		$propertyMappingConfiguration->allowAllProperties();
		$propertyMappingConfiguration->forProperty('image')
			->setTypeConverterOptions(
				'Visol\\Easyvote\\Property\\TypeConverter\\UploadedFileReferenceConverter',
				$uploadConfiguration
			);
	}

	/**
	 * action update
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
	 * @return string
	 */
	public function updateAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption) {
		if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
			$this->votingOptionRepository->update($votingOption);
			$this->persistenceManager->persistAll();
			return json_encode(array('success' => TRUE));
		} else {
			return json_encode(array('success' => FALSE, 'message' => 'Access denied.'));
		}
	}

	/**
	 * action delete
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption
	 * @ignorevalidation $votingOption
	 * @return string
	 */
	public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption) {
		if ($this->isCurrentUserOwnerOfPanel($votingOption->getVoting()->getPanel())) {
			$this->votingOptionRepository->remove($votingOption);
			$this->persistenceManager->persistAll();
			return json_encode(array('removeElement' => TRUE));
		}
	}

	/**
	 * action sort
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @param array $sorting
	 * @return string
	 */
	public function sortAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting, $sorting) {
		if ($this->isCurrentUserOwnerOfPanel($voting->getPanel())) {
			$votingOptions = $this->votingOptionRepository->findByVoting($voting);
			foreach ($votingOptions as $votingOption) {
				/** @var $votingOption \Visol\EasyvoteEducation\Domain\Model\VotingOption */
				$votingOption->setSorting((int)$sorting[$votingOption->getUid()]);
				$this->votingOptionRepository->update($votingOption);
			}
			$this->persistenceManager->persistAll();
			return json_encode(array('success' => TRUE));
		}
	}
}
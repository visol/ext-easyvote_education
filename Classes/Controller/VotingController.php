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

/**
 * VotingController
 */
class VotingController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * votingRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;

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
	 * @return void
	 */
	public function showAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		$this->view->assign('voting', $voting);
	}

	/**
	 * action new
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $newVoting
	 * @ignorevalidation $newVoting
	 * @return void
	 */
	public function newAction(\Visol\EasyvoteEducation\Domain\Model\Voting $newVoting = NULL) {
		$this->view->assign('newVoting', $newVoting);
	}

	/**
	 * action create
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $newVoting
	 * @return void
	 */
	public function createAction(\Visol\EasyvoteEducation\Domain\Model\Voting $newVoting) {
		$this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->votingRepository->add($newVoting);
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @ignorevalidation $voting
	 * @return void
	 */
	public function editAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		$this->view->assign('voting', $voting);
	}

	/**
	 * action update
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return void
	 */
	public function updateAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		$this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->votingRepository->update($voting);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Voting $voting
	 * @return void
	 */
	public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\Voting $voting) {
		$this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->votingRepository->remove($voting);
		$this->redirect('list');
	}

}
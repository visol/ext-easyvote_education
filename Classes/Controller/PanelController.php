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
 * PanelController
 */
class PanelController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * panelRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository = NULL;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$panels = $this->panelRepository->findAll();
		$this->view->assign('panels', $panels);
	}

	/**
	 * action show
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $panel
	 * @return void
	 */
	public function showAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel) {
		$this->view->assign('panel', $panel);
	}

	/**
	 * action new
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $newPanel
	 * @ignorevalidation $newPanel
	 * @return void
	 */
	public function newAction(\Visol\EasyvoteEducation\Domain\Model\Panel $newPanel = NULL) {
		$this->view->assign('newPanel', $newPanel);
	}

	/**
	 * action create
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $newPanel
	 * @return void
	 */
	public function createAction(\Visol\EasyvoteEducation\Domain\Model\Panel $newPanel) {
		$this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->panelRepository->add($newPanel);
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $panel
	 * @ignorevalidation $panel
	 * @return void
	 */
	public function editAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel) {
		$this->view->assign('panel', $panel);
	}

	/**
	 * action update
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $panel
	 * @return void
	 */
	public function updateAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel) {
		$this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->panelRepository->update($panel);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \Visol\EasyvoteEducation\Domain\Model\Panel $panel
	 * @return void
	 */
	public function deleteAction(\Visol\EasyvoteEducation\Domain\Model\Panel $panel) {
		$this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->panelRepository->remove($panel);
		$this->redirect('list');
	}

	/**
	 * action listForCurrentUser
	 *
	 * @return void
	 */
	public function listForCurrentUserAction() {
		
	}

}
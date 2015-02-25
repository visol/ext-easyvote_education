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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Visol\Easyvote\Utility\Algorithms;
use Visol\EasyvoteEducation\Domain\Model\Panel;

/**
 * PanelController
 */
class PanelController extends \Visol\EasyvoteEducation\Controller\AbstractController {

	/**
	 * panelRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository = NULL;

	/**
	 * @var \Visol\Easyvote\Service\CloneService
	 * @inject
	 */
	public $cloneService;

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
	 * @param Panel $panel
	 * @return void
	 */
	public function showAction(Panel $panel) {
		$this->view->assign('panel', $panel);
	}

	/**
	 * action new
	 *
	 * @param Panel $newPanel
	 * @ignorevalidation $newPanel
	 * @return void
	 */
	public function newAction(Panel $newPanel = NULL) {
		if ($this->getLoggedInUser()) {
			$this->view->assign('newPanel', $newPanel);
		} else {
			// todo access denied
		}
	}

	/**
	 * Correct parsing of datetime-local input
	 */
	protected function initializeCreateAction(){
		$propertyMappingConfiguration = $this->arguments['newPanel']->getPropertyMappingConfiguration();
		$propertyMappingConfiguration->forProperty('date')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d\TH:i');
	}

	/**
	 * action create
	 *
	 * @param Panel $newPanel
	 * @return string
	 */
	public function createAction(Panel $newPanel) {
		if ($communityUser = $this->getLoggedInUser()) {
			do {
				$panelId = Algorithms::generateRandomString(8, 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789');
			} while ($this->panelRepository->findOneByPanelId($panelId) instanceof Panel);
			$newPanel->setPanelId($panelId);
			$newPanel->setCommunityUser($communityUser);
			$this->panelRepository->add($newPanel);
			$this->persistenceManager->persistAll();
			$message = LocalizationUtility::translate('panel.actions.create.success', $this->request->getControllerExtensionName(), array($newPanel->getTitle()));
			$this->addFlashMessage($message, '', AbstractMessage::OK);
			return json_encode(array(
				'redirectToAction' => 'managePanels'
			));
		} else {
			// TODO no user logged in
		}
	}

	/**
	 * action edit
	 *
	 * @param Panel $panel
	 * @ignorevalidation $panel
	 * @return void
	 */
	public function editAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$this->view->assign('panel', $panel);
		} else {
			// todo permission denied
		}
	}

	/**
	 * action update
	 *
	 * @param Panel $panel
	 * @return string
	 */
	public function updateAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$message = LocalizationUtility::translate('panel.actions.update.success', $this->request->getControllerExtensionName(), array($panel->getTitle()));
			$this->addFlashMessage($message, '', AbstractMessage::OK);
			$this->panelRepository->update($panel);
			$this->persistenceManager->persistAll();
			return json_encode(array(
				'redirectToAction' => 'managePanels'
			));
		} else {
			// todo permission denied
		}
	}

	/**
	 * action delete
	 *
	 * @param Panel $panel
	 * @return string
	 */
	public function deleteAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$message = LocalizationUtility::translate('panel.actions.delete.success', $this->request->getControllerExtensionName(), array($panel->getTitle()));
			$this->addFlashMessage($message, '', AbstractMessage::OK);
			$this->panelRepository->remove($panel);
			$this->persistenceManager->persistAll();
			return json_encode(array(
				'redirectToAction' => 'managePanels'
			));
		} else {
			// todo permission denied
		}
	}

	/**
	 * action duplicate
	 *
	 * @param Panel $panel
	 * @return string
	 */
	public function duplicateAction(Panel $panel) {
		if ($this->isCurrentUserOwnerOfPanel($panel)) {
			$message = LocalizationUtility::translate('panel.actions.duplicate.success', $this->request->getControllerExtensionName(), array($panel->getTitle()));
			$this->addFlashMessage($message, '', AbstractMessage::OK);
			/** @var \Visol\EasyvoteEducation\Domain\Model\Panel $duplicatePanel */
			$duplicatePanel = $this->cloneService->copy($panel);
			// generate a new panelId
			do {
				$panelId = Algorithms::generateRandomString(8, 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789');
			} while ($this->panelRepository->findOneByPanelId($panelId) instanceof Panel);
			$duplicatePanel->setPanelId($panelId);
			// Prefix "Copy of" to duplicated panel
			$copyOfText = LocalizationUtility::translate('panel.actions.duplicate.copyOf', $this->request->getControllerExtensionName());
			$duplicatePanel->setTitle($copyOfText . ' ' . $panel->getTitle());

			$this->panelRepository->add($duplicatePanel);
			$this->persistenceManager->persistAll();
			return json_encode(array(
				'redirectToAction' => 'managePanels'
			));
		} else {
			// todo permission denied
		}

	}

	/**
	 * action startup
	 */
	public function startupAction() {
	}

	/**
	 * action dashboard
	 */
	public function dashboardAction() {
	}

	/**
	 * action managePanels
	 *
	 * @return void
	 */
	public function managePanelsAction() {
		if ($communityUser = $this->getLoggedInUser()) {
			$this->view->assign('panels', $this->panelRepository->findByCommunityUser($communityUser));
		} else {
			// todo no user logged in
		}
	}

	/**
	 * action startPanel
	 *
	 * @return void
	 */
	public function startPanelAction() {
		if ($communityUser = $this->getLoggedInUser()) {
			$this->view->assign('panels', $communityUser->getPanels());
		} else {
			// todo no user logged in
		}
	}

	/**
	 * Check if the currently logged in user is the owner of a panel
	 *
	 * @param Panel $panel
	 * @return bool
	 */
	public function isCurrentUserOwnerOfPanel(Panel $panel) {
		if ($communityUser = $this->getLoggedInUser()) {
			if ($panel->getCommunityUser() === $communityUser) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

}
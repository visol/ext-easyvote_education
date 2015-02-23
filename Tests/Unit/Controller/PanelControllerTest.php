<?php
namespace Visol\EasyvoteEducation\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Lorenz Ulrich <lorenz.ulrich@visol.ch>, visol digitale Dienstleistungen GmbH
 *  			
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Visol\EasyvoteEducation\Controller\PanelController.
 *
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class PanelControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Visol\EasyvoteEducation\Controller\PanelController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Visol\\EasyvoteEducation\\Controller\\PanelController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPanelsFromRepositoryAndAssignsThemToView() {

		$allPanels = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$panelRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\PanelRepository', array('findAll'), array(), '', FALSE);
		$panelRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPanels));
		$this->inject($this->subject, 'panelRepository', $panelRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('panels', $allPanels);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenPanelToView() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('panel', $panel);

		$this->subject->showAction($panel);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenPanelToView() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newPanel', $panel);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($panel);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenPanelToPanelRepository() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$panelRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\PanelRepository', array('add'), array(), '', FALSE);
		$panelRepository->expects($this->once())->method('add')->with($panel);
		$this->inject($this->subject, 'panelRepository', $panelRepository);

		$this->subject->createAction($panel);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenPanelToView() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('panel', $panel);

		$this->subject->editAction($panel);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenPanelInPanelRepository() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$panelRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\PanelRepository', array('update'), array(), '', FALSE);
		$panelRepository->expects($this->once())->method('update')->with($panel);
		$this->inject($this->subject, 'panelRepository', $panelRepository);

		$this->subject->updateAction($panel);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenPanelFromPanelRepository() {
		$panel = new \Visol\EasyvoteEducation\Domain\Model\Panel();

		$panelRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\PanelRepository', array('remove'), array(), '', FALSE);
		$panelRepository->expects($this->once())->method('remove')->with($panel);
		$this->inject($this->subject, 'panelRepository', $panelRepository);

		$this->subject->deleteAction($panel);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPanelsFromRepositoryAndAssignsThemToView() {

		$allPanels = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$panelRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\PanelRepository', array('findAll'), array(), '', FALSE);
		$panelRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPanels));
		$this->inject($this->subject, 'panelRepository', $panelRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('panels', $allPanels);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}
}

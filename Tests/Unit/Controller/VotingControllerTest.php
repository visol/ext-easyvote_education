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
 * Test case for class Visol\EasyvoteEducation\Controller\VotingController.
 *
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class VotingControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Visol\EasyvoteEducation\Controller\VotingController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Visol\\EasyvoteEducation\\Controller\\VotingController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllVotingsFromRepositoryAndAssignsThemToView() {

		$allVotings = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$votingRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\VotingRepository', array('findAll'), array(), '', FALSE);
		$votingRepository->expects($this->once())->method('findAll')->will($this->returnValue($allVotings));
		$this->inject($this->subject, 'votingRepository', $votingRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('votings', $allVotings);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenVotingToView() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('voting', $voting);

		$this->subject->showAction($voting);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenVotingToView() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newVoting', $voting);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($voting);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenVotingToVotingRepository() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$votingRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\VotingRepository', array('add'), array(), '', FALSE);
		$votingRepository->expects($this->once())->method('add')->with($voting);
		$this->inject($this->subject, 'votingRepository', $votingRepository);

		$this->subject->createAction($voting);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenVotingToView() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('voting', $voting);

		$this->subject->editAction($voting);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenVotingInVotingRepository() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$votingRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\VotingRepository', array('update'), array(), '', FALSE);
		$votingRepository->expects($this->once())->method('update')->with($voting);
		$this->inject($this->subject, 'votingRepository', $votingRepository);

		$this->subject->updateAction($voting);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenVotingFromVotingRepository() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();

		$votingRepository = $this->getMock('Visol\\EasyvoteEducation\\Domain\\Repository\\VotingRepository', array('remove'), array(), '', FALSE);
		$votingRepository->expects($this->once())->method('remove')->with($voting);
		$this->inject($this->subject, 'votingRepository', $votingRepository);

		$this->subject->deleteAction($voting);
	}
}

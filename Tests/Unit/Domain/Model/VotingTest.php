<?php

namespace Visol\EasyvoteEducation\Tests\Unit\Domain\Model;

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
 * Test case for class \Visol\EasyvoteEducation\Domain\Model\Voting.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class VotingTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Visol\EasyvoteEducation\Domain\Model\Voting
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Visol\EasyvoteEducation\Domain\Model\Voting();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() {
		$this->subject->setTitle('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'title',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getShortReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getShort()
		);
	}

	/**
	 * @test
	 */
	public function setShortForStringSetsShort() {
		$this->subject->setShort('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'short',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getIsVisibleReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getIsVisible()
		);
	}

	/**
	 * @test
	 */
	public function setIsVisibleForBooleanSetsIsVisible() {
		$this->subject->setIsVisible(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'isVisible',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getIsVotingEnabledReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getIsVotingEnabled()
		);
	}

	/**
	 * @test
	 */
	public function setIsVotingEnabledForBooleanSetsIsVotingEnabled() {
		$this->subject->setIsVotingEnabled(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'isVotingEnabled',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getVotingDurationReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getVotingDuration()
		);
	}

	/**
	 * @test
	 */
	public function setVotingDurationForIntegerSetsVotingDuration() {
		$this->subject->setVotingDuration(12);

		$this->assertAttributeEquals(
			12,
			'votingDuration',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getVotingOptionsReturnsInitialValueForVotingOption() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getVotingOptions()
		);
	}

	/**
	 * @test
	 */
	public function setVotingOptionsForObjectStorageContainingVotingOptionSetsVotingOptions() {
		$votingOption = new \Visol\EasyvoteEducation\Domain\Model\VotingOption();
		$objectStorageHoldingExactlyOneVotingOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneVotingOptions->attach($votingOption);
		$this->subject->setVotingOptions($objectStorageHoldingExactlyOneVotingOptions);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneVotingOptions,
			'votingOptions',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addVotingOptionToObjectStorageHoldingVotingOptions() {
		$votingOption = new \Visol\EasyvoteEducation\Domain\Model\VotingOption();
		$votingOptionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$votingOptionsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($votingOption));
		$this->inject($this->subject, 'votingOptions', $votingOptionsObjectStorageMock);

		$this->subject->addVotingOption($votingOption);
	}

	/**
	 * @test
	 */
	public function removeVotingOptionFromObjectStorageHoldingVotingOptions() {
		$votingOption = new \Visol\EasyvoteEducation\Domain\Model\VotingOption();
		$votingOptionsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$votingOptionsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($votingOption));
		$this->inject($this->subject, 'votingOptions', $votingOptionsObjectStorageMock);

		$this->subject->removeVotingOption($votingOption);

	}
}

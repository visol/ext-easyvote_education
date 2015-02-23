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
 * Test case for class \Visol\EasyvoteEducation\Domain\Model\VotingOption.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class VotingOptionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Visol\EasyvoteEducation\Domain\Model\VotingOption
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Visol\EasyvoteEducation\Domain\Model\VotingOption();
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
	public function getStyleReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getStyle()
		);
	}

	/**
	 * @test
	 */
	public function setStyleForIntegerSetsStyle() {
		$this->subject->setStyle(12);

		$this->assertAttributeEquals(
			12,
			'style',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCachedVotesReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getCachedVotes()
		);
	}

	/**
	 * @test
	 */
	public function setCachedVotesForIntegerSetsCachedVotes() {
		$this->subject->setCachedVotes(12);

		$this->assertAttributeEquals(
			12,
			'cachedVotes',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCachedRankReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getCachedRank()
		);
	}

	/**
	 * @test
	 */
	public function setCachedRankForIntegerSetsCachedRank() {
		$this->subject->setCachedRank(12);

		$this->assertAttributeEquals(
			12,
			'cachedRank',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getImageReturnsInitialValueForFileReference() {
		$this->assertEquals(
			NULL,
			$this->subject->getImage()
		);
	}

	/**
	 * @test
	 */
	public function setImageForFileReferenceSetsImage() {
		$fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
		$this->subject->setImage($fileReferenceFixture);

		$this->assertAttributeEquals(
			$fileReferenceFixture,
			'image',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getVotesReturnsInitialValueForVote() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getVotes()
		);
	}

	/**
	 * @test
	 */
	public function setVotesForObjectStorageContainingVoteSetsVotes() {
		$vote = new \Visol\EasyvoteEducation\Domain\Model\Vote();
		$objectStorageHoldingExactlyOneVotes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneVotes->attach($vote);
		$this->subject->setVotes($objectStorageHoldingExactlyOneVotes);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneVotes,
			'votes',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addVoteToObjectStorageHoldingVotes() {
		$vote = new \Visol\EasyvoteEducation\Domain\Model\Vote();
		$votesObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$votesObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($vote));
		$this->inject($this->subject, 'votes', $votesObjectStorageMock);

		$this->subject->addVote($vote);
	}

	/**
	 * @test
	 */
	public function removeVoteFromObjectStorageHoldingVotes() {
		$vote = new \Visol\EasyvoteEducation\Domain\Model\Vote();
		$votesObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$votesObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($vote));
		$this->inject($this->subject, 'votes', $votesObjectStorageMock);

		$this->subject->removeVote($vote);

	}
}

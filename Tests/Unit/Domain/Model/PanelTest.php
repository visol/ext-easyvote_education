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
 * Test case for class \Visol\EasyvoteEducation\Domain\Model\Panel.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Lorenz Ulrich <lorenz.ulrich@visol.ch>
 */
class PanelTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Visol\EasyvoteEducation\Domain\Model\Panel
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Visol\EasyvoteEducation\Domain\Model\Panel();
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
	public function getDescriptionReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getDescription()
		);
	}

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() {
		$this->subject->setDescription('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'description',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDateReturnsInitialValueForDateTime() {
		$this->assertEquals(
			NULL,
			$this->subject->getDate()
		);
	}

	/**
	 * @test
	 */
	public function setDateForDateTimeSetsDate() {
		$dateTimeFixture = new \DateTime();
		$this->subject->setDate($dateTimeFixture);

		$this->assertAttributeEquals(
			$dateTimeFixture,
			'date',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getRoomReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getRoom()
		);
	}

	/**
	 * @test
	 */
	public function setRoomForStringSetsRoom() {
		$this->subject->setRoom('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'room',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getAddressReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getAddress()
		);
	}

	/**
	 * @test
	 */
	public function setAddressForStringSetsAddress() {
		$this->subject->setAddress('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'address',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getOrganizationReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getOrganization()
		);
	}

	/**
	 * @test
	 */
	public function setOrganizationForStringSetsOrganization() {
		$this->subject->setOrganization('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'organization',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getClassReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getClass()
		);
	}

	/**
	 * @test
	 */
	public function setClassForStringSetsClass() {
		$this->subject->setClass('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'class',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getNumberOfParticipantsReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getNumberOfParticipants()
		);
	}

	/**
	 * @test
	 */
	public function setNumberOfParticipantsForStringSetsNumberOfParticipants() {
		$this->subject->setNumberOfParticipants('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'numberOfParticipants',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTermsAcceptedReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getTermsAccepted()
		);
	}

	/**
	 * @test
	 */
	public function setTermsAcceptedForBooleanSetsTermsAccepted() {
		$this->subject->setTermsAccepted(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'termsAccepted',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCityReturnsInitialValueForCity() {	}

	/**
	 * @test
	 */
	public function setCityForCitySetsCity() {	}

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
	public function getCreatorReturnsInitialValueForCommunityUser() {	}

	/**
	 * @test
	 */
	public function setCreatorForCommunityUserSetsCreator() {	}

	/**
	 * @test
	 */
	public function getVotingsReturnsInitialValueForVoting() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getVotings()
		);
	}

	/**
	 * @test
	 */
	public function setVotingsForObjectStorageContainingVotingSetsVotings() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();
		$objectStorageHoldingExactlyOneVotings = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneVotings->attach($voting);
		$this->subject->setVotings($objectStorageHoldingExactlyOneVotings);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneVotings,
			'votings',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addVotingToObjectStorageHoldingVotings() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();
		$votingsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$votingsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($voting));
		$this->inject($this->subject, 'votings', $votingsObjectStorageMock);

		$this->subject->addVoting($voting);
	}

	/**
	 * @test
	 */
	public function removeVotingFromObjectStorageHoldingVotings() {
		$voting = new \Visol\EasyvoteEducation\Domain\Model\Voting();
		$votingsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$votingsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($voting));
		$this->inject($this->subject, 'votings', $votingsObjectStorageMock);

		$this->subject->removeVoting($voting);

	}
}

<?php
namespace Visol\EasyvoteEducation\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Visol\EasyvoteEducation\Domain\Model\Panel;
use TYPO3\CMS\Core\Resource\File as FalFile;
use TYPO3\CMS\Core\Resource\FileReference as FalFileReference;

class VotingService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
     * @inject
     */
    protected $votingRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\VotingOptionRepository
     * @inject
     */
    protected $votingOptionRepository = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    protected $resourceFactory;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @param Panel $panel
     * @return \Visol\EasyvoteEducation\Domain\Model\Voting
     */
    public function getNextVoting(Panel $panel)
    {
        if (!$panel->getCurrentVoting() instanceof \Visol\EasyvoteEducation\Domain\Model\Voting) {
            // We have no current state, so we're at the beginning of the panel and need the first voting
            return $this->votingRepository->findFirstVotingByPanel($panel);
        } else {
            return $this->votingRepository->findNextVotingByPanelAndCurrentVoting($panel, $panel->getCurrentVoting());
        }
    }

    /**
     * @param $currentState
     * @return \Visol\EasyvoteEducation\Domain\Model\Voting
     */
    public function getCurrentVoting($currentState)
    {
        // currentState is in format action/uid
        $currentStateArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('-', $currentState, true);
        if (count($currentStateArray)) {
            return $this->votingRepository->findByUid((int)$currentStateArray[1]);
        } else {
            return null;
        }

    }

    /**
     * @param Panel $panel
     * @param $votingStepAction
     * @return string
     */
    public function getViewNameForCurrentPanelState(Panel $panel, $votingStepAction)
    {
        // currentState is in format action/uid
        $currentStateArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('-', $panel->getCurrentState(), true);
        if (count($currentStateArray) === 2) {
            return $currentStateArray[0];
        } else {
            // if no currentState is set, keep the passed $votingStepAction
            return $votingStepAction;
        }
    }

    /**
     * @param Panel $panel
     */
    public function processVotingResult(Panel $panel)
    {
        $voting = $panel->getCurrentVoting();
        $votesCount = 0;
        foreach ($voting->getVotingOptions() as $votingOption) {
            /** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
            $votesCountForVotingOption = $votingOption->getVotes()->count();
            $votingOption->setCachedVotes($votesCountForVotingOption);
            $votesCount = $votesCount + $votesCountForVotingOption;
            $this->votingOptionRepository->update($votingOption);
        }
        $this->persistenceManager->persistAll();

        // $votesCount is complete
        foreach ($voting->getVotingOptions() as $votingOption) {
            /** @var \Visol\EasyvoteEducation\Domain\Model\VotingOption $votingOption */
            if ($votesCount > 0) {
                $votingResult = round($votingOption->getCachedVotes() / $votesCount, 5) * 100;
                $votingOption->setCachedVotingResult((int)$votingResult);
            } else {
                $votingOption->setCachedVotingResult(0);
            }
            $this->votingOptionRepository->update($votingOption);
        }
        $this->persistenceManager->persistAll();
    }

    /**
     * Get a file reference for a placeholder image
     *
     * @param string $type
     * @return null|\Visol\Easyvote\Domain\Model\FileReference
     */
    public function getPlaceholderImageFileReference($type = 'random')
    {
        $pathToPlaceholderImages = 'EXT:easyvote_education/Resources/Public/Images/';
        $randomPlaceholderImageFilenames = array(
            'placeholder-ammann.jpg',
            'placeholder-berset.jpg',
            'placeholder-burkhalter.jpg',
            'placeholder-leuthard.jpg',
            'placeholder-maurer.jpg',
            'placeholder-sommaruga.jpg',
            'placeholder-widmerschlumpf.jpg',
        );

        switch ($type) {
            case 'yes':
                $placeholderImagePathAndFilename = $pathToPlaceholderImages . 'placeholder-yes.png';
                break;
            case 'no':
                $placeholderImagePathAndFilename = $pathToPlaceholderImages . 'placeholder-no.png';
                break;
            case 'abstention':
                $placeholderImagePathAndFilename = $pathToPlaceholderImages . 'placeholder-abstention.png';
                break;
            case 'random':
            default:
                $key = array_rand($randomPlaceholderImageFilenames);
                $randomPlaceholderImageFilename = $randomPlaceholderImageFilenames[$key];
                $placeholderImagePathAndFilename = $pathToPlaceholderImages . $randomPlaceholderImageFilename;
                break;
        }

        $fileObject = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->retrieveFileOrFolderObject($placeholderImagePathAndFilename);

        if ($fileObject instanceof FalFile) {
            return $this->createFileReferenceFromFalFileObject($fileObject);
        } else {
            return null;
        }
    }

    /**
     * @param FalFile $file
     * @param int $resourcePointer
     * @return \Visol\Easyvote\Domain\Model\FileReference
     */
    protected function createFileReferenceFromFalFileObject(FalFile $file, $resourcePointer = null)
    {
        $fileReference = $this->resourceFactory->createFileReferenceObject(
            array(
                'uid_local' => $file->getUid(),
                'uid_foreign' => uniqid('NEW_'),
                'uid' => uniqid('NEW_'),
            )
        );
        return $this->createFileReferenceFromFalFileReferenceObject($fileReference, $resourcePointer);
    }

    /**
     * @param FalFileReference $falFileReference
     * @param int $resourcePointer
     * @return \Visol\Easyvote\Domain\Model\FileReference
     */
    protected function createFileReferenceFromFalFileReferenceObject(
        FalFileReference $falFileReference,
        $resourcePointer = null
    ) {
        if ($resourcePointer === null) {
            /** @var $fileReference \Visol\Easyvote\Domain\Model\FileReference */
            $fileReference = $this->objectManager->get('Visol\\Easyvote\\Domain\\Model\\FileReference');

        } else {
            $fileReference = $this->persistenceManager->getObjectByIdentifier($resourcePointer,
                'Visol\\Easyvote\\Domain\\Model\\FileReference', false);
        }

        $fileReference->setOriginalResource($falFileReference);
        $fileReference->_setProperty('_languageUid', -1);

        return $fileReference;
    }

}
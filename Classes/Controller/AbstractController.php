<?php
namespace Visol\EasyvoteEducation\Controller;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;

class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Visol\Easyvote\Domain\Repository\CommunityUserRepository
     * @inject
     */
    protected $communityUserRepository;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
     * @inject
     */
    protected $panelRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
     * @inject
     */
    protected $votingRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\PanelInvitationRepository
     * @inject
     */
    protected $panelInvitationRepository = null;

    /**
     * @var \Visol\Easyvote\Domain\Repository\PartyRepository
     * @inject
     */
    protected $partyRepository = null;

    /**
     * @var \Visol\Easyvote\Domain\Repository\KantonRepository
     * @inject
     */
    protected $kantonRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\VotingOptionRepository
     * @inject
     */
    protected $votingOptionRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Domain\Repository\VoteRepository
     * @inject
     */
    protected $voteRepository = null;

    /**
     * @var \Visol\EasyvoteEducation\Service\DummyDataService
     * @inject
     */
    protected $dummyDataService = null;

    /**
     * @var \Visol\Easyvote\Service\CloneService
     * @inject
     */
    public $cloneService;

    /**
     * @var \Visol\Easyvote\Service\CommunityUserService
     * @inject
     */
    public $communityUserService;

    /**
     * @var \Visol\EasyvoteEducation\Service\VotingService
     * @inject
     */
    protected $votingService;

    /**
     * @var \TYPO3\CMS\Extbase\Service\ExtensionService
     * @inject
     */
    protected $extensionService;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @return \Visol\Easyvote\Domain\Model\CommunityUser|bool
     */
    protected function getLoggedInUser()
    {
        if ((int)$GLOBALS['TSFE']->fe_user->user['uid'] > 0) {
            $communityUser = $this->communityUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            if ($communityUser instanceof \Visol\Easyvote\Domain\Model\CommunityUser) {
                return $communityUser;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if the currently logged in user is the owner of a panel
     *
     * @param Panel $panel
     * @return bool
     */
    public function isCurrentUserOwnerOfPanel(Panel $panel)
    {
        if ($communityUser = $this->getLoggedInUser()) {
            if ($panel->getCommunityUser() === $communityUser) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Check if the currently logged in user is the owner of a panel
     *
     * @return FALSE|\Visol\Easyvote\Domain\Model\Party
     */
    public function getPartyIfCurrentUserIsAdministrator()
    {
        if ($communityUser = $this->getLoggedInUser()) {
            if ($communityUser->isPartyAdministrator()) {
                // Party is a lazy property of CommunityUser
                if ($communityUser->getParty() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
                    $communityUser->getParty()->_loadRealInstance();
                }
                return $communityUser->getParty();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Debugs a SQL query from a QueryResult
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult
     * @param boolean $explainOutput
     * @return void
     */
    public function debugQuery(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult, $explainOutput = false)
    {
        $GLOBALS['TYPO3_DB']->debugOuput = 2;
        if ($explainOutput) {
            $GLOBALS['TYPO3_DB']->explainOutput = true;
        }
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        $queryResult->toArray();
        var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
        $GLOBALS['TYPO3_DB']->explainOutput = false;
        $GLOBALS['TYPO3_DB']->debugOuput = false;
    }

    /**
     * Gets the POST data from the requests and returns all data in the plugin namespace if defined
     *
     * @return array|null
     */
    protected function getPostData()
    {
        $data = [];
        $pluginNamespace = $this->extensionService->getPluginNamespace($this->request->getControllerExtensionName(),
            $this->request->getPluginName());
        $requestBody = file_get_contents('php://input');
        parse_str($requestBody, $data);
        return array_key_exists($pluginNamespace, $data) ? $data[$pluginNamespace] : null;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }

}

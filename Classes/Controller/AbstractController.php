<?php
namespace Visol\EasyvoteEducation\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Lorenz Ulrich <lorenz.ulrich@visol.ch>, visol digitale Dienstleistungen GmbH
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * communityUserRepository
	 *
	 * @var \Visol\Easyvote\Domain\Repository\CommunityUserRepository
	 * @inject
	 */
	protected $communityUserRepository;

	/**
	 * panelRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\PanelRepository
	 * @inject
	 */
	protected $panelRepository = NULL;

	/**
	 * votingRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;

	/**
	 * votingOptionRepository
	 *
	 * @var \Visol\EasyvoteEducation\Domain\Repository\VotingOptionRepository
	 * @inject
	 */
	protected $votingOptionRepository = NULL;

	/**
	 * dummyDataService
	 *
	 * @var \Visol\EasyvoteEducation\Service\DummyDataService
	 * @inject
	 */
	protected $dummyDataService = NULL;

	/**
	 * @var \Visol\Easyvote\Service\CloneService
	 * @inject
	 */
	public $cloneService;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * @return \Visol\Easyvote\Domain\Model\CommunityUser|bool
	 */
	protected function getLoggedInUser() {
		if ((int)$GLOBALS['TSFE']->fe_user->user['uid'] > 0) {
			$communityUser = $this->communityUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
			if ($communityUser instanceof \Visol\Easyvote\Domain\Model\CommunityUser) {
				return $communityUser;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
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

	/**
	 * @param string $viewType One of Template, Partial, Layout
	 * @param string $filename Filename of requested template/partial/layout, may be prefixed with subfolders
	 * @return string
	 */
	public function resolveViewFileForStandaloneView($viewType, $filename) {
		$extbaseConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'easyvote', 'easyvote');
		if (array_key_exists(lcfirst($viewType) . 'RootPath', $extbaseConfiguration['view'])) {
			// deprecated singular setting
			return GeneralUtility::getFileAbsFileName($extbaseConfiguration['view']['templateRootPath'] . $filename);
		} else {
			// new setting, reverse array (because highest priority is last)
			$viewTypeConfigurationArray = array_reverse($extbaseConfiguration['view'][lcfirst($viewType) . 'RootPaths']);
			// check if the requested file exists at location and return the first file found
			foreach ($viewTypeConfigurationArray as $viewTypeConfiguration) {
				if (file_exists(GeneralUtility::getFileAbsFileName($viewTypeConfiguration . $filename))) {
					return GeneralUtility::getFileAbsFileName($viewTypeConfiguration . $filename);
				}
			}
		}
	}

}
?>
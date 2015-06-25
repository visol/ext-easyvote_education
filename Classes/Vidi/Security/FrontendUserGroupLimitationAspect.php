<?php
namespace Visol\EasyvoteEducation\Vidi\Security;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Persistence\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class FrontendUserGroupLimitationAspect {

	/**
	 * Post-process the matcher object to respect the file storages.
	 *
	 * @param Matcher $matcher
	 * @param string $dataType
	 * @return void
	 */
	public function addUsergroupConstraint(Matcher $matcher, $dataType) {
		if ($dataType === 'fe_users' && $GLOBALS['_SERVER']['REQUEST_METHOD'] === 'GET' && $this->getModuleLoader()->getSignature() === 'easyvote_VidiFeUsersM1') {
			$matcher->in('usergroup.uid', array(9, 10));
		}
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @return \Fab\Vidi\Module\ModuleLoader
	 */
	protected function getModuleLoader() {
		return GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader');
	}
}

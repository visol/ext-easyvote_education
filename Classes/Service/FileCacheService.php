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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Visol\EasyvoteEducation\Domain\Model\Panel;

class FileCacheService
{

    /**
     * @var string
     */
    const CACHE_FILE_DIRECTORY = 'typo3temp/EasyvoteEducation';

    /**
     * @var Panel
     */
    protected $paned;

    /**
     * FileCacheService constructor.
     * @param Panel $panel
     */
    public function __construct(Panel $panel)
    {
        $this->panel = $panel;
    }

    /**
     * @return $this
     */
    public function initialize()
    {
        $cacheDirectory = $this->getAbsolutePath();
        if (!is_dir($cacheDirectory)) {
            GeneralUtility::mkdir_deep($cacheDirectory);
        }

        $statusFile = $this->getStatusFile();
        if (!is_file($statusFile)) {
            $defaultState = ['state' => 'init'];
            GeneralUtility::writeFile($statusFile, json_encode($defaultState));
        }

        $contentFile = $this->getContentFile();
        if (!is_file($contentFile)) {
            GeneralUtility::writeFile($contentFile, ''); // empty file for now
        }

        return $this;
    }

    /**
     * @param string $newState
     * @return $this
     */
    public function changeState($newState)
    {
        $state = ['state' => $newState];
        $json = GeneralUtility::getUrl($this->getContentUrl());
        $content = json_decode($json, true);
        GeneralUtility::writeFile($this->getContentFile(), $content['content']);
        GeneralUtility::writeFile($this->getStatusFile(), json_encode($state));
        return $this;
    }

    /**
     * @return string
     */
    protected function getContentUrl()
    {
        return sprintf( 'http%s://%s/routing/votings/panel-%s-guestViewContent-0?L=%s',
            GeneralUtility::getIndpEnv('TYPO3_SSL') ? 's' : '',
            GeneralUtility::getIndpEnv('HTTP_HOST'),
            $this->panel->getUid(),
            $this->getFrontendObject()->sys_language_uid
        );
    }

    /**
     * @return string
     */
    protected function getStatusFile()
    {
        return $this->getAbsolutePath() . DIRECTORY_SEPARATOR . 'state.json';
    }

    /**
     * @return string
     */
    protected function getContentFile()
    {
        return $this->getAbsolutePath() . DIRECTORY_SEPARATOR . 'content.html';
    }

    /**
     * @return string
     */
    protected function getAbsolutePath()
    {
        return PATH_site . $this->getRelativePath();
    }

    /**
     * @return string
     */
    public function getRelativePath()
    {
        return self::CACHE_FILE_DIRECTORY . DIRECTORY_SEPARATOR . $this->panel->getPanelId();
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
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
    /**
     * Contains an implementation of the mediaWizardProvider supporting some
     * well known providers.
     *
     * @author Aishwara M.B.<aishu.moorthy@gmail.com>
     * @author Steffen Kamper <info@sk-typo3.de>
     * @author Ernesto Baschny <ernst@cron-it.de>
     */
/**
 * Class MediaWizardProvider
 * @package Visol\EasyvoteEducation\Service
 * @deprecated use \Visol\Easyvote\Service\MediaWizardProvider instead
 */
class MediaWizardProvider extends \TYPO3\CMS\Frontend\MediaWizard\MediaWizardProvider
{

    /***********************************************
     *
     * Providers URL rewriting:
     *
     ***********************************************/
    /**
     * Parse youtube url
     *
     * @param string $url
     * @return string processed url
     */
    protected function process_youtube($url)
    {
        $videoId = '';

        $pattern = '%
		^(?:https?://)?									# Optional URL scheme Either http or https
		(?:www\.)?										# Optional www subdomain
		(?:												# Group host alternatives:
			youtu\.be/									#  Either youtu.be/,
			|youtube(?:									#  or youtube.com/
				-nocookie								#   optional nocookie domain
			)?\.com/(?:
				[^/]+/.+/								#   Either /something/other_params/ for channels,
				|(?:v|e(?:								#   or v/ or e/,
					mbed								#    optional mbed for embed/
				)?)/
				|.*[?&]v=								#   or ?v= or ?other_param&v=
			)
		)												# End host alternatives.
		([^"&?/ ]{11})									# 11 characters (Length of Youtube video ids).
		(?:.+)?$										# Optional other ending URL parameters.
		%xs';
        if (preg_match($pattern, $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            $url = $this->getUrlSchema() . 'www.youtube.com/embed/' . $videoId . '?fs=1';
        }
        return $url;
    }

}

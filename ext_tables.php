<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Managepanels',
	'easyvote Education: Podien verwalten (für Lehrperson)'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Panelparticipations',
	'easyvote Education: Podienteilnahmen verwalten (für Politiker)'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Panelassignment',
	'easyvote Education: Podienteilnahmen verwalten (für Partei-Administrator)'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_panelassignment';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/flexform_panelassignment.xml');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Guestview',
	'easyvote Education: Podiumsteilnahme als Gast'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Presentationview',
	'easyvote Education: Präsentationsansicht (für Lehrperson)'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'easyvote Education');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_panel');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_voting');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_votingoption');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_vote');

// Backend Module for managing Panels
$panelTable = 'tx_easyvoteeducation_domain_model_panel';

/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader', $panelTable);

$moduleLoader->setIcon(sprintf('EXT:easyvote_education/Resources/Public/Icons/%s.png', $panelTable))
	->setMainModule('easyvote')
	->setModuleLanguageFile(sprintf('LLL:EXT:easyvote_education/Resources/Private/Language/Vidi/%s.xlf', $panelTable))
	/*->addJavaScriptFile(sprintf('EXT:easyvote_education/Resources/Public/JavaScript/%s.js', $panelTable))*/
	->setDefaultPid(286) // hard-coded for now
	->register();

// Backend Module for managing Frontend Users
$usersTable = 'fe_users';

/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader', $usersTable);

$moduleLoader->setIcon(sprintf('EXT:easyvote_education/Resources/Public/Icons/%s.png', $usersTable))
	->setMainModule('easyvote')
	->setModuleLanguageFile(sprintf('LLL:EXT:easyvote_education/Resources/Private/Language/Vidi/%s.xlf', $usersTable))
	/*->addJavaScriptFile(sprintf('EXT:easyvote_education/Resources/Public/JavaScript/%s.js', $panelTable))*/
	->setDefaultPid(144) // hard-coded for now
	->register();


// Signal slot to filter the Frontend Users to only display teachers and politicians
/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
	'Fab\Vidi\Controller\Backend\ContentController',
	'postProcessMatcherObject',
	'Visol\EasyvoteEducation\Vidi\Security\FrontendUserGroupLimitationAspect',
	'addUsergroupConstraint',
	TRUE
);
<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Managepanels',
	'easyvote Education: Podien verwalten'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'easyvote Education');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_easyvoteeducation_domain_model_panel', 'EXT:easyvote_education/Resources/Private/Language/locallang_csh_tx_easyvoteeducation_domain_model_panel.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_panel');
$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_panel'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,description,date,room,address,organization,class,number_of_participants,terms_accepted,city,image,creator,votings,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Panel.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_easyvoteeducation_domain_model_panel.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_easyvoteeducation_domain_model_voting', 'EXT:easyvote_education/Resources/Private/Language/locallang_csh_tx_easyvoteeducation_domain_model_voting.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_voting');
$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_voting'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,short,is_visible,is_voting_enabled,voting_duration,voting_options,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Voting.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_easyvoteeducation_domain_model_voting.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_easyvoteeducation_domain_model_votingoption', 'EXT:easyvote_education/Resources/Private/Language/locallang_csh_tx_easyvoteeducation_domain_model_votingoption.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_votingoption');
$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_votingoption'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,style,cached_votes,cached_rank,image,votes,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/VotingOption.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_easyvoteeducation_domain_model_votingoption.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_easyvoteeducation_domain_model_vote', 'EXT:easyvote_education/Resources/Private/Language/locallang_csh_tx_easyvoteeducation_domain_model_vote.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_easyvoteeducation_domain_model_vote');
$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_vote'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_vote',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => '',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Vote.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_easyvoteeducation_domain_model_vote.gif'
	),
);

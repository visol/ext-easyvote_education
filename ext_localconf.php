<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Managepanels',
	array(
		'Panel' => 'startup, dashboard, managePanels, startPanel, new, create, edit, update, delete, duplicate, editVotings, execute, votingStep',
		'Voting' => 'listForCurrentUser, edit, update, delete, new, duplicate, sort',
		'VotingOption' => 'listForVoting, new,edit,update,delete,sort'

	),
	// non-cacheable actions
	array(
		'Panel' => 'managePanels, startPanel, new, create, edit, update, delete, duplicate, editVotings, execute, votingStep',
		'Voting' => 'listForCurrentUser, edit, update, delete, new, duplicate, sort',
		'VotingOption' => 'listForVoting, new,edit,update,delete,sort',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Guestview',
	array(
		'Panel' => 'guestViewLogin, guestViewParticipation, viewEventStream',

	),
	// non-cacheable actions
	array(
		'Panel' => 'guestViewParticipation, viewEventStream',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Presentationview',
	array(
		'Panel' => 'presentationViewLogin, presentationViewParticipation, viewEventStream',

	),
	// non-cacheable actions
	array(
		'Panel' => 'presentationViewParticipation, viewEventStream',
	)
);

// Register global route
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['routing']['globalRoutes'][] = 'EXT:easyvote_education/Configuration/GlobalRoutes.yaml';

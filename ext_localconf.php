<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Managepanels',
	array(
		'Panel' => 'managePanelsStartup, managePanels, new, create, edit, update, delete, duplicate, editVotings, editPanelInvitations, execute, votingStep',
		'Voting' => 'listForCurrentUser, update, delete, new, duplicate, sort',
		'PanelInvitation' => 'listForCurrentUser, create, delete, getAvailablePartiesForPanel',
		'VotingOption' => 'listForVoting, new, update,delete,sort'

	),
	// non-cacheable actions
	array(
		'Panel' => 'managePanels, new, create, edit, update, delete, duplicate, editVotings, editPanelInvitations, execute, votingStep',
		'Voting' => 'listForCurrentUser, update, delete, new, duplicate, sort',
		'PanelInvitation' => 'listForCurrentUser, create, delete, getAvailablePartiesForPanel',
		'VotingOption' => 'listForVoting, new, update, delete, sort',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Panelparticipations',
	array(
		'Panel' => 'panelParticipationsStartup, panelParticipations',
		'PanelInvitation' => 'attend,ignore',

	),
	// non-cacheable actions
	array(
		'Panel' => 'managePanels, panelParticipations',
		'PanelInvitation' => 'attend,ignore',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Panelassignment',
	array(
		'PanelInvitation' => 'manageInvitations,listForPartyByDemand,assignUser,removeUser,filter',
		'PartyMember' => 'getMembersOfCurrentParty',

	),
	// non-cacheable actions
	array(
		'PanelInvitation' => 'manageInvitations,listForPartyByDemand,assignUser,removeUser,filter',
		'PartyMember' => 'getMembersOfCurrentParty',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Guestview',
	array(
		'Panel' => 'guestViewLogin, guestViewParticipation',

	),
	// non-cacheable actions
	array(
		'Panel' => 'guestViewParticipation',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Presentationview',
	array(
		'Panel' => 'presentationViewLogin, presentationViewParticipation',

	),
	// non-cacheable actions
	array(
		'Panel' => 'presentationViewParticipation',
	)
);

// Register global route
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['routing']['globalRoutes'][] = 'EXT:easyvote_education/Configuration/GlobalRoutes.yaml';

// Register EID for EventStream
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['easyvoteeducation'] = 'EXT:easyvote_education/Resources/Private/Eid/EventStream.php';

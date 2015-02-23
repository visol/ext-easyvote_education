<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Managepanels',
	array(
		'Panel' => 'listForCurrentUser',
		
	),
	// non-cacheable actions
	array(
		'Panel' => 'create, update, delete, ',
		'Voting' => 'create, update, delete',
		
	)
);

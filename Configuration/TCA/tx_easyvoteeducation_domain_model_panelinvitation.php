<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_panelinvitation'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panelinvitation',
		'label' => 'allowed_parties',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

//		'versioningWS' => 2,
//		'versioning_followPages' => TRUE,

//		'languageField' => 'sys_language_uid',
//		'transOrigPointerField' => 'l10n_parent',
//		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',

		),
		'searchFields' => 'allowed_parties,attending_community_user,ignoring_community_users,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('easyvote_education') . 'Resources/Public/Icons/tx_easyvoteeducation_domain_model_panelinvitation.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, allowed_parties, attending_community_user, ignoring_community_users',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, allowed_parties, attending_community_user, ignoring_community_users, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_easyvoteeducation_domain_model_panelinvitation',
				'foreign_table_where' => 'AND tx_easyvoteeducation_domain_model_panelinvitation.pid=###CURRENT_PID### AND tx_easyvoteeducation_domain_model_panelinvitation.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),

		'allowed_parties' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panelinvitation.allowed_parties',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_easyvote_domain_model_party',
				'MM' => 'tx_easyvoteeducation_panelinvitation_party_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'enableMultiSelectFilterTextfield' => TRUE,
				'wizards' => array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => array(
						'type' => 'popup',
						'title' => 'Edit',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new',
						'icon' => 'add.gif',
						'params' => array(
							'table' => 'tx_easyvote_domain_model_party',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
							),
						'script' => 'wizard_add.php',
					),
				),
			),
		),
		'attending_community_user' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panelinvitation.attending_community_user',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'fe_users',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'ignoring_community_users' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panelinvitation.ignoring_community_users',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'fe_users',
				'MM' => 'tx_easyvoteeducation_panelinvitation_communityuser_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'enableMultiSelectFilterTextfield' => TRUE,
				'wizards' => array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => array(
						'type' => 'popup',
						'title' => 'Edit',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new',
						'icon' => 'add.gif',
						'params' => array(
							'table' => 'fe_users',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
							),
						'script' => 'wizard_add.php',
					),
				),
			),
		),
		
		'panel' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);

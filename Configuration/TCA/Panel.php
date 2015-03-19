<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_panel'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_easyvoteeducation_domain_model_panel']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, panel_id, title, description, date, room, address, organization, class, number_of_participants, terms_accepted, city, image, votings',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, panel_id, title, description, date, room, address, organization, class, number_of_participants, terms_accepted, city, image, votings, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_easyvoteeducation_domain_model_panel',
				'foreign_table_where' => 'AND tx_easyvoteeducation_domain_model_panel.pid=###CURRENT_PID### AND tx_easyvoteeducation_domain_model_panel.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'panel_id' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.panel_id',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'date' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.date',
			'config' => array(
				'dbType' => 'datetime',
				'type' => 'input',
				'size' => 12,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => '0000-00-00 00:00:00'
			),
		),
		'room' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.room',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'address' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.address',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'organization' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.organization',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'class' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.class',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'number_of_participants' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.number_of_participants',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'current_state' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.current_state',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'terms_accepted' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.terms_accepted',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'city' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.city',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_easyvote_domain_model_city',
				'items'   => array(
					array('', ''),
				),
			),
		),
		'image' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.image',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('fal_image', array(
				'appearance' => array(
					'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
				),
				'foreign_match_fields' => array(
					'fieldname' => 'image',
					'tablenames' => 'tx_easyvoteeducation_domain_model_panel',
					'table_local' => 'sys_file',
				),
				'minitems' => 0,
				'maxitems' => 1,
			), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
		),
		'votings' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.votings',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_easyvoteeducation_domain_model_voting',
				'foreign_field' => 'panel',
				'foreign_sortby' => 'sorting',
				'maxitems'      => 9999,
				'appearance' => array(
					'collapseAll' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'useSortable' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
		'community_user' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.community_user',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'fe_users',
				//'readOnly' => 1,
				'items'   => array(
					array('', ''),
				),
			),
		),
	),
);

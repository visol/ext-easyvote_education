<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_voting'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_easyvoteeducation_domain_model_voting']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, short, is_visible, is_voting_enabled, voting_duration, voting_options',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, title, short, is_visible, is_voting_enabled, voting_duration, voting_options, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_easyvoteeducation_domain_model_voting',
				'foreign_table_where' => 'AND tx_easyvoteeducation_domain_model_voting.pid=###CURRENT_PID### AND tx_easyvoteeducation_domain_model_voting.sys_language_uid IN (-1,0)',
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

		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'short' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.short',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'is_visible' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.is_visible',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'is_voting_enabled' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.is_voting_enabled',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'voting_duration' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.voting_duration',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'voting_options' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_voting.voting_options',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_easyvoteeducation_domain_model_votingoption',
				'foreign_field' => 'voting',
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
		'panel' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_panel.panel',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_easyvoteeducation_domain_model_panel',
				'items'   => array(
					array('', ''),
				),
			),
		),
		'sorting' => array(
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int',
				'readOnly' => 1
			)
		),
	),
);

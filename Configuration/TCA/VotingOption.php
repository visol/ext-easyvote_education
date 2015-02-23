<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_easyvoteeducation_domain_model_votingoption'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_easyvoteeducation_domain_model_votingoption']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, style, cached_votes, cached_rank, image, votes',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, title, style, cached_votes, cached_rank, image, votes, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
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
				'foreign_table' => 'tx_easyvoteeducation_domain_model_votingoption',
				'foreign_table_where' => 'AND tx_easyvoteeducation_domain_model_votingoption.pid=###CURRENT_PID### AND tx_easyvoteeducation_domain_model_votingoption.sys_language_uid IN (-1,0)',
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
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'style' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.style',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('-- Label --', 0),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => ''
			),
		),
		'cached_votes' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.cached_votes',
			'config' => array(
				'readOnly' => 1,
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'cached_rank' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.cached_rank',
			'config' => array(
				'readOnly' => 1,
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'image' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.image',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('fal_image', array(
				'appearance' => array(
					'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
				),
				'foreign_match_fields' => array(
					'fieldname' => 'image',
					'tablenames' => 'tx_easyvoteeducation_domain_model_votingoption',
					'table_local' => 'sys_file',
				),
				'minitems' => 0,
				'maxitems' => 1,
			), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
		),
		'votes' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:easyvote_education/Resources/Private/Language/locallang_db.xlf:tx_easyvoteeducation_domain_model_votingoption.votes',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_easyvoteeducation_domain_model_vote',
				'foreign_field' => 'votingoption',
				'maxitems'      => 9999,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),

		),
		
		'voting' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
	),
);

<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Remove all facets first
unset($GLOBALS['TCA']['fe_users']['grid']['facets']);

// Exclude more fields from TCA.
$tca = [
    'grid' => [
        'excluded_fields' => $GLOBALS['TCA']['fe_users']['grid']['excluded_fields'] . ', uid, name, crdate, tstamp, password, middle_name, address, fax, title, zip, city, country, www, company, fal_image, starttime, endtime, lastlogin, tx_extbase_type, customer_number, age_start, age_end, datasets, salutation, kanton, notification_mail_active, notification_sms_active, notification_related_users, community_user, party_verification_code,tx_cabagloginas_loginas, tx_easyvoteeducation_panels',
        'facets' => [
            'uid',
            'usergroup',
            new \Fab\Vidi\Facet\StandardFacet(
                'privacy_protection',
                'LLL:EXT:easyvote/Resources/Private/Language/locallang_db.xlf:tx_easyvote_domain_model_communityuser.privacy_protection',
                [
                    '1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
                    '0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
                ]
            ),
            new \Fab\Vidi\Facet\StandardFacet(
                'notification_mail_active',
                'LLL:EXT:easyvote/Resources/Private/Language/locallang_db.xlf:tx_easyvote_domain_model_communityuser.notification_mail_active',
                [
                    '1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
                    '0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
                ]
            ),
            new \Fab\Vidi\Facet\StandardFacet(
                'notification_sms_active',
                'LLL:EXT:easyvote/Resources/Private/Language/locallang_db.xlf:tx_easyvote_domain_model_communityuser.notification_sms_active',
                [
                    '1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
                    '0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
                ]
            ),
            new \Fab\Vidi\Facet\StandardFacet(
                'community_news_mail_active',
                'LLL:EXT:easyvote/Resources/Private/Language/locallang_db.xlf:tx_easyvote_domain_model_communityuser.community_news_mail_active',
                [
                    '1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
                    '0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
                ]
            ),
            new \Fab\Vidi\Facet\StandardFacet(
                'disable',
                'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active',
                [
                    '0' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.0',
                    '1' => 'LLL:EXT:vidi/Resources/Private/Language/locallang.xlf:active.1'
                ]
            ),
            'username',
            'first_name',
            'last_name',
            'telephone',
            'email',
        ],
    ]];

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);
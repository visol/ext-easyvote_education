<?php
if (!defined('TYPO3_MODE')) { die ('Access denied.'); }

// Exclude more fields from TCA.
$tca = array(
	'grid' => array(
		'excluded_fields' => $GLOBALS['TCA']['fe_users']['grid']['excluded_fields'] . ', uid, name, crdate, tstamp, password, middle_name, address, fax, title, zip, city, country, www, company, fal_image, starttime, endtime, lastlogin, tx_extbase_type, customer_number, age_start, age_end, datasets, salutation, kanton, notification_mail_active, notification_sms_active, notification_related_users, community_user, party_verification_code,tx_cabagloginas_loginas, tx_easyvoteeducation_panels',
	),
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);
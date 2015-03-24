<?php

/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $TSFE */
$TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
$TSFE->set_no_cache();
$TSFE->connectToDB();

header('Content-Type: text/event-stream');
echo "retry: 2000" . PHP_EOL;
echo "id: " . time() . PHP_EOL;
$panelUid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('panelUid');
$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('current_state', 'tx_easyvoteeducation_domain_model_panel', 'uid = ' . $panelUid);
echo "data: " . $row['current_state'] . PHP_EOL;
echo "event: currentState" . PHP_EOL;
echo PHP_EOL;
ob_flush();
flush();
exit();
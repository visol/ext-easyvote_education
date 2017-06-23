<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('PATH_typo3conf')) die ('Could not access this script directly!');


/**
 * Class CastVote
 */
class CastVote
{

    /**
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected $frontendUserAuthentication;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * Constructor
     */
    public function __construct()
    {

        /** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $TSFE */
        $TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
        $TSFE->set_no_cache();
        $TSFE->connectToDB();

        // Initialize FE user object:
        $this->frontendUserAuthentication = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();

        $arguments = GeneralUtility::_GET('arguments');
        $explodedArguments = GeneralUtility::trimExplode('-', $arguments);
        $this->arguments['panelUid'] = isset($explodedArguments[1]) ? (int)$explodedArguments[1] : 0;
        $this->arguments['votingOptionUid'] = isset($explodedArguments[3]) ? (int)$explodedArguments[3] : 0;
    }

    /**
     * @return bool
     */
    protected function castVote()
    {
        $result = false;

        $votingOption = $this->getVotingOption();
        if ($votingOption) {

            $voting = $this->getVoting($votingOption['voting']);
            if ($voting && $voting['is_voting_enabled']) {

                $condensedVotingName = GeneralUtility::_GET('arguments');

                if ($this->frontendUserAuthentication->getSessionData('easyvoteeducation-castVote') !== $condensedVotingName) {

                    $newVoteIdentifier = $this->createNewVote($votingOption);
                    if ($newVoteIdentifier > 0) {
                        $result = $this->increaseVoting($voting);
                    }

                    // save information about cast vote to session to prevent double-casting
                    $this->frontendUserAuthentication->setAndSaveSessionData('easyvoteeducation-castVote', $condensedVotingName);
                } else {
                    // Vote was cast before and it is ignored
                }
            }
        }
        return $result;
    }

    /**
     * @param array $voting
     * @return bool
     */
    protected function increaseVoting(array $voting)
    {
        $tableName = 'tx_easyvoteeducation_domain_model_voting';
        $values = [
            'voting_options' => $voting['voting_options'] + 1,
        ];

        return (bool)$this->getDatabaseConnection()->exec_UPDATEquery($tableName, 'uid = ' . $voting['uid'], $values);
    }

    /**
     * @param array $votingOption
     * @return int
     */
    protected function createNewVote(array $votingOption)
    {
        $tableName = 'tx_easyvoteeducation_domain_model_vote';
        $values = [
            'votingoption' => $votingOption['uid'],
            'tstamp' => time(),
            'crdate' => time(),
            'pid' => 286,
        ];

        $this->getDatabaseConnection()->exec_INSERTquery($tableName, $values);
        return $this->getDatabaseConnection()->sql_insert_id();
    }

    /**
     * @param int $voteIdentifier
     * @return array|FALSE|NULL
     */
    protected function getVoting($voteIdentifier)
    {
        $tableName = 'tx_easyvoteeducation_domain_model_voting';
        $clause = 'deleted = 0 AND hidden = 0 AND uid = ' . (int)$voteIdentifier;
        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);
        return (array)$record;
    }

    /**
     * @return array
     */
    protected function getVotingOption()
    {
        $tableName = 'tx_easyvoteeducation_domain_model_votingoption';
        $clause = 'deleted = 0 AND hidden = 0 AND uid = ' . $this->arguments['votingOptionUid'];
        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);
        return (array)$record;
    }

    /**
     *
     */
    public function output()
    {
        print (int)$this->castVote();
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}

$castVote = new CastVote();
$castVote->output();

//header('Content-Type: text/event-stream');
//echo "retry: 2000" . PHP_EOL;
//echo "id: " . time() . PHP_EOL;
//$panelUid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('panelUid');
//$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('current_state', 'tx_easyvoteeducation_domain_model_panel', 'uid = ' . $panelUid);
//echo "data: " . $row['current_state'] . PHP_EOL;
//echo "event: currentState" . PHP_EOL;
echo PHP_EOL;
ob_flush();
flush();
exit();
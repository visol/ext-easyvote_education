#
# Table structure for table 'tx_easyvoteeducation_domain_model_panel'
#
CREATE TABLE tx_easyvoteeducation_domain_model_panel (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	panel_id varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	date datetime DEFAULT '0000-00-00 00:00:00',
	room varchar(255) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	organization varchar(255) DEFAULT '' NOT NULL,
	class varchar(255) DEFAULT '' NOT NULL,
	number_of_participants varchar(255) DEFAULT '' NOT NULL,
	current_state varchar(255) DEFAULT '' NOT NULL,
	terms_accepted tinyint(1) unsigned DEFAULT '0' NOT NULL,
	city int(11) unsigned DEFAULT '0',
	image int(11) unsigned DEFAULT '0',
	creator int(11) unsigned DEFAULT '0',
	votings int(11) unsigned DEFAULT '0' NOT NULL,
	community_user int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_voting'
#
CREATE TABLE tx_easyvoteeducation_domain_model_voting (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	panel int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	short varchar(255) DEFAULT '' NOT NULL,
	is_visible tinyint(1) unsigned DEFAULT '0' NOT NULL,
	is_voting_enabled tinyint(1) unsigned DEFAULT '0' NOT NULL,
	voting_duration int(11) DEFAULT '0' NOT NULL,
	voting_options int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_votingoption'
#
CREATE TABLE tx_easyvoteeducation_domain_model_votingoption (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	voting int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	style int(11) DEFAULT '0' NOT NULL,
	cached_votes int(11) DEFAULT '0' NOT NULL,
	cached_rank int(11) DEFAULT '0' NOT NULL,
	cached_voting_result int(11) DEFAULT '0' NOT NULL,
	image int(11) unsigned DEFAULT '0',
	votes int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_vote'
#
CREATE TABLE tx_easyvoteeducation_domain_model_vote (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	votingoption int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),

 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_voting'
#
CREATE TABLE tx_easyvoteeducation_domain_model_voting (

	panel  int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_votingoption'
#
CREATE TABLE tx_easyvoteeducation_domain_model_votingoption (

	voting  int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_easyvoteeducation_domain_model_vote'
#
CREATE TABLE tx_easyvoteeducation_domain_model_vote (

	votingoption  int(11) unsigned DEFAULT '0' NOT NULL,

);

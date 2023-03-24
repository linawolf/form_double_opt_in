#
# Table structure for table 'tx_formdoubleoptin_domain_model_optin'
#
CREATE TABLE tx_formdoubleoptin_domain_model_optin (

    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

    pagelanguage tinyint(4) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    given_name varchar(255) DEFAULT '' NOT NULL,
    family_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    company varchar(255) DEFAULT '' NOT NULL,
    customer_number varchar(255) DEFAULT '' NOT NULL,
    mail_body text,

    is_validated tinyint(4) unsigned DEFAULT '0' NOT NULL,
    validation_hash varchar(255) DEFAULT '' NOT NULL,
    registration_date int(11) unsigned DEFAULT '0' NOT NULL,
    validation_date int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY hash (validation_hash)
);

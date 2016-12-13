CREATE TABLE WakeOnLan (
  ID smallint(5) unsigned NOT NULL auto_increment,
  ip varchar(15) NOT NULL default '',
  mac varchar(17) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY ip (ip)
) TYPE=MyISAM;


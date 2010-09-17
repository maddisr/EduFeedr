
CREATE TABLE IF NOT EXISTS prefix_edufeedr_course_participants (
	id bigint(20) unsigned NOT NULL auto_increment,
	course_guid bigint(20) unsigned NOT NULL,
	firstname varchar(255) NOT NULL,
	lastname varchar(255) NOT NULL,
	email varchar(255) NOT NULL,
	blog varchar(255) NOT NULL,
	posts varchar(255) NOT NULL,
	comments varchar(255) NOT NULL,
	blogger varchar(255) DEFAULT '',
	created TIMESTAMP DEFAULT NOW(),
	modified TIMESTAMP,
	status enum ('active','inactive','teacher') NOT NULL DEFAULT 'active',
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS prefix_edufeedr_course_assignments (
	id bigint(20) unsigned NOT NULL auto_increment,
	course_guid bigint(20) unsigned NOT NULL,
	title varchar(255) NOT NULL,
	description text NOT NULL,
	blog_post_url varchar(255) NOT NULL,
	deadline varchar(255) NOT NULL,
	created TIMESTAMP DEFAULT NOW(),
	modified TIMESTAMP,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS prefix_edufeedr_course_facilitators (
	id bigint(20) unsigned NOT NULL auto_increment,
	course_guid bigint(20) unsigned NOT NULL,
	user_guid bigint(20) unsigned NOT NULL,
	PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

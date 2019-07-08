<?php

DB::mysqlConnection($mysql);

$mysql->query("CREATE TABLE IF NOT EXISTS `cases` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `templateUrl` varchar(60) NOT NULL,
  `controller` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");

$mysql->query('SELECT count(*) AS count FROM cases')->fetch()->count;

# Invullen van de data als de tabel leeg is
if ( ! $mysql->query('SELECT count(*) AS count FROM cases')->fetch()->count)
{
    $mysql->query("INSERT INTO `cases` (`id`, `name`, `templateUrl`, `controller`) VALUES
    (1, 'Sql Injections',              'case1.html', 'CaseA1Controller'),
    (2, 'Stored XSS',                  'case2.html', 'CaseA32Controller'),
    (3, 'Reflected XSS',               'case3.html', 'CaseA3Controller'),
    (4, 'Indirect object reference',   'case4.html', 'CaseA4Controller'),
    (5, 'Security misconfiguration',   'case5.html', 'CaseA5Controller'),
    (6, 'Sensitive data exposure',   'case6.html', 'CaseA6Controller')");
}

$mysql->query("CREATE TABLE IF NOT EXISTS `user` (
  `token` varchar(32) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");

/**
 * Create the user_case table
 */
$mysql->query("CREATE TABLE IF NOT EXISTS `user_case` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `user_token` varchar(32) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'none',
  `score` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
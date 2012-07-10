delimiter $$

CREATE TABLE `mdl_unit_tracker_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(500) DEFAULT NULL,
  `coursename` varchar(500) DEFAULT NULL,
  `employerid` varchar(500) DEFAULT NULL,
  `moodle_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moodle_id` (`moodle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_effectiveness` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_effectiveness_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learner_ref` varchar(45) DEFAULT NULL,
  `effect_id` varchar(45) DEFAULT NULL,
  `effectiveness_score` varchar(45) DEFAULT NULL,
  `comment` varchar(7000) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_employers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) DEFAULT NULL,
  `colours` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_marks_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) DEFAULT NULL,
  `colours` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_marks_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `courseid` varchar(45) DEFAULT NULL,
  `description` varchar(600) DEFAULT NULL,
  `markid` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`courseid`)
) ENGINE=InnoDB AUTO_INCREMENT=244 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_units_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `unitid` varchar(45) DEFAULT NULL,
  `markid` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_user_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `learner_ref` varchar(250) DEFAULT NULL,
  `course_code` varchar(45) DEFAULT NULL,
  `employer` varchar(45) DEFAULT NULL,
  `rep_name` varchar(45) DEFAULT NULL,
  `employer_id` varchar(45) DEFAULT NULL,
  `training_centre` varchar(45) DEFAULT NULL,
  `programme_start_date` varchar(45) DEFAULT NULL,
  `officer` varchar(45) DEFAULT NULL,
  `moodle_id` varchar(45) DEFAULT NULL,
  `course_moodle_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moodle_id` (`moodle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13641 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_user_criteria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `criteria_id` varchar(45) DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `target` varchar(500) DEFAULT NULL,
  `evidence` varchar(500) DEFAULT NULL,
  `colour` varchar(45) DEFAULT NULL,
  `moodle_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moolde_id` (`moodle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=960 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_user_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` varchar(45) DEFAULT NULL,
  `user_id` varchar(45) DEFAULT NULL,
  `target` varchar(500) DEFAULT NULL,
  `evidence` varchar(500) DEFAULT NULL,
  `colour` varchar(45) DEFAULT NULL,
  `moodle_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moodle_id` (`moodle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3382 DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `mdl_unit_tracker_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moodle_id` varchar(45) DEFAULT NULL,
  `learner_ref` varchar(45) DEFAULT NULL,
  `rep_name` varchar(45) DEFAULT NULL,
  `employer_id` varchar(450) DEFAULT NULL,
  `training_centre` varchar(45) DEFAULT NULL,
  `programme_start_date` datetime DEFAULT NULL,
  `officer` varchar(45) DEFAULT NULL,
  `course_id` varchar(45) DEFAULT NULL,
  `employer` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moodle_id` (`moodle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8$$



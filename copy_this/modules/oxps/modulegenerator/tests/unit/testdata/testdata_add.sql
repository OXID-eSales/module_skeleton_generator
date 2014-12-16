-- Test data creation script

-- Sample CMS snippet insertion
INSERT INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`, `OXTIMESTAMP`) VALUES ('oxpstestident', 'oxpstestident', 'oxbaseshop', '1', '0', '1', '1', '', 'Test', '<p>Hello,</p> <p><i>World!</i></p> ', 'Test', '<p>Hello,</p> <p><i>World!</i></p> ', '1', 'Test', '<p>Hello,</p> <p><i>World!</i></p> ', '1', '', '<p>Hello,</p> <p><i>World!</i></p> ', '30e44ab83fdee7564.23264141', '', '', CURRENT_TIMESTAMP);

-- Model related test table

--
-- Table structure for table `testspecial_offer`
--

CREATE TABLE IF NOT EXISTS `testspecial_offer` (
  `OXID` char(32) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`OXID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

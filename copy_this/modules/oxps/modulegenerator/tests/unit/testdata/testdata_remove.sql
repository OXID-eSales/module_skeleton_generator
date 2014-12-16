-- Test data removal script

-- Sample CMS snippet deletion
DELETE FROM `oxcontents` WHERE `OXID` = 'oxpstestident';

-- Model related test table
DROP TABLE IF EXISTS `testspecial_offer`;

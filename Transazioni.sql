/* Table data export for table Transazioni */

/* Preserve session variables */
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS=0;

/* Export data */
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (1,1,3.5,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (2,1,2,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (3,3,3.5,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (4,3,3.5,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (5,4,4,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (6,4,4,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (7,5,3,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (8,5,3,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (9,6,5,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (10,6,5,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (11,7,2.5,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (12,7,2.5,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (13,8,1,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (14,8,1,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (15,9,4,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (16,9,4,"Gennaio","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (17,10,3,"Dicembre","2018");
INSERT INTO `Transazioni` (`_id`,`userId`,`cash`,`mese`,`anno`) VALUES (18,10,3,"Gennaio","2018");

/* Restore session variables to original values */
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

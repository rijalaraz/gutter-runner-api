

# This will create and then populate a MySQL table with a list of the names and
# ISO code abbreviations for the provinces and territories of Canada.
 
 
CREATE TABLE `ca_provinces` (
    `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `name` VARCHAR( 255 ) NOT NULL ,
    `iso` CHAR( 2 ) NOT NULL
);
 
INSERT INTO `ca_provinces` (`id`, `name`, `iso`)
VALUES 
    (NULL, 'Alberta', 'AB'),
    (NULL, 'British Columbia', 'BC'),
    (NULL, 'Manitoba', 'MB'),
    (NULL, 'New Brunswick', 'NB'),
    (NULL, 'Newfoundland and Labrador', 'NL'),
    (NULL, 'Northwest Territories', 'NT'),
    (NULL, 'Nova Scotia', 'NS'),
    (NULL, 'Nunavut', 'NU'),
    (NULL, 'Ontario', 'ON'),
    (NULL, 'Prince Edward Island', 'PE'),
    (NULL, 'Quebec', 'QC'),
    (NULL, 'Saskatchewan', 'SK'),
    (NULL, 'Yukon', 'YT');
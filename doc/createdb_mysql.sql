CREATE DATABASE storeconsent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE storeconsent.consent (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    visitor_id CHAR(64) NOT NULL,
    settings MEDIUMTEXT NOT NULL,
    last_change TIMESTAMP NOT NULL,
    PRIMARY KEY (id),
    UNIQUE INDEX (visitor_id)
) ENGINE = InnoDB;
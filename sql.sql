
CREATE TABLE tm_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name varchar(255),
    password varchar(255),
    profile varchar(255),
    time_limit varchar(255),
    type varchar(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE tm_profil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_name VARCHAR(255) NOT NULL,
    shared_users INT NOT NULL,
    rate_limit VARCHAR(255) NOT NULL,
    session_timeout VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `tm_user` ADD `kode` VARCHAR(255) NOT NULL AFTER `id`;
# Create the database
CREATE DATABASE `database_name`;

# Create the database user
CREATE USER `database_user`@`localhost` IDENTIFIED BY 'password';

# Grant user permissions on the database
GRANT ALL PRIVILEGES ON `database_name`.* TO `database_user`@`localhost`;
FLUSH PRIVILEGES;

# Create the user table and add a test user
CREATE TABLE user(
	id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	email VARCHAR(128) NOT NULL,
	first_name VARCHAR(50) NOT NULL,
	last_name VARCHAR(75) NOT NULL,
	password VARCHAR(255) NOT NULL
);
# Test data
INSERT INTO user (email, first_name, last_name, password) VALUES ('test@test.com', 'Steve', 'Smith', '$2y$10$7FTOIGpvbDt1UawNStDiau54skZvLrmbg0JHrfXILkMHNcJ7Z/eNO');
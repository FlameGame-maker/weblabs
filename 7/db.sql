CREATE TABLE langs (id int(10) unsigned AUTO_INCREMENT PRIMARY KEY,name varchar(50) NOT NULL UNIQUE);

INSERT INTO langs (name) VALUES ('pascal'), ('c'), ('cpp'), ('javascript'), ('php'), ('python'), ('java'), ('haskel'), ('clojure'), ('prolog'), ('scala'), ('go');

CREATE TABLE application (id int(10) unsigned AUTO_INCREMENT PRIMARY KEY, surname varchar(128) NOT NULL DEFAULT '', name varchar(128) NOT NULL DEFAULT '', patronymic varchar(128) NOT NULL DEFAULT '', phone_number varchar(12) NOT NULL DEFAULT '', email varchar(128) NOT NULL DEFAULT '', birthday date NOT NULL DEFAULT '1001-01-01', sex varchar(5) NOT NULL DEFAULT '', biography text NOT NULL);

CREATE TABLE application_langs (application_id int(10) unsigned NOT NULL, lang_id int(10) unsigned NOT NULL, FOREIGN KEY (application_id) REFERENCES application(id) ON DELETE CASCADE, FOREIGN KEY (lang_id) REFERENCES langs(id) ON DELETE CASCADE, PRIMARY KEY (application_id, lang_id));

CREATE TABLE app_users (id int(10) unsigned AUTO_INCREMENT PRIMARY KEY, login varchar(8) NOT NULL, password_hash VARCHAR(255) NOT NULL, application_id int(10) unsigned, FOREIGN KEY (application_id) REFERENCES application(id) ON DELETE CASCADE);

CREATE TABLE xhr_tokens (id int AUTO_INCREMENT PRIMARY KEY, token varchar(255) UNIQUE NOT NULL, user_id int, expires_at datetime NOT NULL, INDEX idx_token (token));


CREATE USER administration;

CREATE DATABASE crowdsourcing;

CREATE SCHEMA crowdsourcing;

-- DOMAINS --

CREATE DOMAIN keyword_type AS VARCHAR(50);

-- TABLES --

CREATE TABLE keyword(
    keyword VARCHAR(50) PRIMARY KEY,
    type keyword_type NOT NULL 
);
CREATE TABLE task(
    id SERIAL PRIMARY KEY,
    description VARCHAR(280),
    title VARCHAR(50),
    n_workers INTEGER,
    threshold INTEGER,
    valid_bit BOOLEAN,
    pay   ,
    campaign INTEGER REFERENCE campaign(id)
);
CREATE TABLE answer(
    value VARCHAR(100) PRIMARY KEY
);
CREATE TABLE worker(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20)? UNIQUE NOT NULL 
);
CREATE TABLE requester(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20)? UNIQUE NOT NULL 
);
CREATE TABLE campaign(
    id SERIAL PRIMARY KEY,
    registration_start_date DATE NOT NULL,
    registration_end_date DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);
------------------------------------------------
CREATE TABLE requires(
    task INTEGER,
    keyword VARCHAR(50),
    PRIMARY KEY(task,keyword),
    FOREIGN key(task) REFERENCES task(id),
    FOREIGN KEY(keyword) REFERENCES keyword(keyword)
);
CREATE TABLE recives(
    
);
CREATE TABLE has(
    worker VARCHAR(20),
    keyword VARCHAR(50),
    score INTEGER,
    PRIMARY KEY(worker,keyword),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
    FOREIGN KEY(keyword) REFERENCES keyword(keyword)
);
CREATE TABLE join(
    worker VARCHAR(20),
    campaign INTEGER,
    PRIMARY KEY(worker,campaign),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
    FOREIGN KEY(campaign) REFERENCES campaign(id),
);





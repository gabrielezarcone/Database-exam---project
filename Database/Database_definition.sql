
CREATE USER administration;

CREATE DATABASE crowdsourcing;

CREATE SCHEMA crowdsourcing;

-- DOMAINS --

CREATE DOMAIN keyword_type AS VARCHAR(50);

-- TABLES --

CREATE TABLE campaign(
    id SERIAL PRIMARY KEY,
    registration_start_date DATE NOT NULL,
    registration_end_date DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);
CREATE TABLE keyword(
    keyword VARCHAR(50) PRIMARY KEY,
    type keyword_type NOT NULL 
);
CREATE TABLE pay(
    type VARCHAR(50) PRIMARY KEY
);
CREATE TABLE task(
    id SERIAL PRIMARY KEY,
    description VARCHAR(280),
    title VARCHAR(50),
    n_workers INTEGER,
    threshold INTEGER,
    valid_bit BOOLEAN,
    campaign INTEGER,
    pay_type VARCHAR(50),
    pay_description VARCHAR(280),
    FOREIGN KEY (pay_type) REFERENCES pay(type),
    FOREIGN KEY (campaign) REFERENCES campaign(id)
);
CREATE TABLE answer(
    task INTEGER,
    value VARCHAR(100),
    PRIMARY KEY(task,value),
    FOREIGN KEY(task) REFERENCES task(id)
);
CREATE TABLE worker(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20)? UNIQUE NOT NULL
);
CREATE TABLE requester(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20)? UNIQUE NOT NULL 
);
------------------------------------------------
CREATE TABLE choose(
    worker VARCHAR(20),
    task INTEGER,
    answer VARCHAR(100),
    PRIMARY KEY(worker),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
    FOREIGN KEY(task,answer) REFERENCES answer(task,value)
);
CREATE TABLE requires(
    task INTEGER,
    keyword VARCHAR(50),
    PRIMARY KEY(task,keyword),
    FOREIGN key(task) REFERENCES task(id),
    FOREIGN KEY(keyword) REFERENCES keyword(keyword)
);
CREATE TABLE recives(
    task INTEGER,
    worker VARCHAR(20),
    valid_bit_user BOOLEAN,
    PRIMARY KEY(task,worker),
    FOREIGN KEY(task) REFERENCES task(id),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
);
CREATE TABLE has(
    worker VARCHAR(20),
    keyword VARCHAR(50),
    score INTEGER,
    PRIMARY KEY(worker,keyword),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
    FOREIGN KEY(keyword) REFERENCES keyword(keyword)
);
CREATE TABLE joins(
    worker VARCHAR(20),
    campaign INTEGER,
    PRIMARY KEY(worker,campaign),
    FOREIGN KEY(worker) REFERENCES worker(user_name),
    FOREIGN KEY(campaign) REFERENCES campaign(id)
);


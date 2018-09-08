
CREATE USER administration;

CREATE DATABASE crowdsourcing;

CREATE SCHEMA crowdsourcing;

-- SET SEARCH PATH --

SET search_path TO crowdsourcing, public;

-- DOMAINS --

CREATE DOMAIN keyword_type AS VARCHAR(50)
    CHECK(VALUE IN ('knoledge','attitude'));

CREATE DOMAIN pay_type AS VARCHAR(50)
    CHECK(VALUE IN ('money','coupon','promotionalCode','freeItem'));   --coupon = money to be spent in a particular website or shop
                                                                          --free item = a phisical object. es: they give you a microowave as a prize
                                                                          --promotionalCode = anything that give you a code
-- TABLES --

CREATE TABLE worker(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20) NOT NULL
);
CREATE TABLE requester(
    user_name VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20) NOT NULL 
);
CREATE TABLE campaign(
    id SERIAL PRIMARY KEY,
    registration_start_date DATE NOT NULL,
    registration_end_date DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    requester VARCHAR(20) NOT NULL,
    FOREIGN KEY(requester) REFERENCES requester(user_name) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK(registration_start_date >= now()),
    CHECK(registration_end_date > now()),
    CHECK(registration_end_date > registration_start_date),
    CHECK(start_date >= now()),
    CHECK(end_date > now()),
    CHECK(end_date > start_date)
);
CREATE TABLE keyword(
    keyword VARCHAR(50) PRIMARY KEY,
    type keyword_type NOT NULL   ---kwnoledge or attitude
);
CREATE TABLE pay(
    type pay_type PRIMARY KEY
);
CREATE TABLE task(
    id SERIAL PRIMARY KEY,
    description VARCHAR(280),
    title VARCHAR(50) NOT NULL,
    n_workers INTEGER NOT NULL,
    threshold INTEGER NOT NULL,
    valid_bit BOOLEAN DEFAULT 'FALSE' NOT NULL,
    campaign INTEGER NOT NULL,
    pay_type VARCHAR(50) NOT NULL,
    pay_description VARCHAR(280) NOT NULL,
    FOREIGN KEY (pay_type) REFERENCES pay(type) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (campaign) REFERENCES campaign(id) ON UPDATE NO ACTION ON DELETE NO ACTION,
    CHECK(n_workers > 0),
    CHECK(threshold > 0)
);
CREATE TABLE answer(
    task INTEGER,
    value VARCHAR(100),
    PRIMARY KEY(task,value),
    FOREIGN KEY(task) REFERENCES task(id) ON UPDATE CASCADE ON DELETE CASCADE
);

------------------------------------------------
CREATE TABLE choose(
    worker VARCHAR(20),
    task INTEGER NOT NULL,
    answer VARCHAR(100) NOT NULL,
    PRIMARY KEY(worker),
    FOREIGN KEY(worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY(task,answer) REFERENCES answer(task,value) ON UPDATE NO ACTION ON DELETE NO ACTION ---?SETNULL--?SETNULL
);
CREATE TABLE requires_keyword(
    task INTEGER,
    keyword VARCHAR(50),
    PRIMARY KEY(task,keyword),
    FOREIGN key(task) REFERENCES task(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(keyword) REFERENCES keyword(keyword) ON UPDATE CASCADE ON DELETE NO ACTION
);
CREATE TABLE recives_task(
    task INTEGER,
    worker VARCHAR(20),
    valid_bit_user BOOLEAN DEFAULT 'FALSE' NOT NULL,
    PRIMARY KEY(task,worker),
    FOREIGN KEY(task) REFERENCES task(id) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY(worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE
);
CREATE TABLE has_keyword(
    worker VARCHAR(20),
    keyword VARCHAR(50),
    score INTEGER NOT NULL,
    PRIMARY KEY(worker,keyword),
    FOREIGN KEY(worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(keyword) REFERENCES keyword(keyword) ON UPDATE CASCADE ON DELETE NO ACTION,
    CHECK(score >= 0)
);
CREATE TABLE joins_campaign(
    worker VARCHAR(20),
    campaign INTEGER,
    PRIMARY KEY(worker,campaign),
    FOREIGN KEY(worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(campaign) REFERENCES campaign(id) ON UPDATE CASCADE ON DELETE NO ACTION
);


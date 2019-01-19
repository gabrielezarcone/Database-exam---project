--
-- PostgreSQL database dump
--

-- Dumped from database version 10.0
-- Dumped by pg_dump version 10.0

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: crowdsourcing; Type: SCHEMA; Schema: -; Owner: gabriele
--

CREATE SCHEMA crowdsourcing;


ALTER SCHEMA crowdsourcing OWNER TO gabriele;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = crowdsourcing, pg_catalog;

--
-- Name: keyword_type; Type: DOMAIN; Schema: crowdsourcing; Owner: gabriele
--

CREATE DOMAIN keyword_type AS character varying(50)
	CONSTRAINT keyword_type_check CHECK (((VALUE)::text = ANY ((ARRAY['knowledge'::character varying, 'attitude'::character varying])::text[])));


ALTER DOMAIN keyword_type OWNER TO gabriele;

--
-- Name: pay_type; Type: DOMAIN; Schema: crowdsourcing; Owner: gabriele
--

CREATE DOMAIN pay_type AS character varying(50)
	CONSTRAINT pay_type_check CHECK (((VALUE)::text = ANY ((ARRAY['money'::character varying, 'coupon'::character varying, 'promotionalCode'::character varying, 'freeItem'::character varying])::text[])));


ALTER DOMAIN pay_type OWNER TO gabriele;

SET search_path = public, pg_catalog;

--
-- Name: answered_task_num(character varying); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION answered_task_num(character varying) RETURNS TABLE(answered bigint)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task  
                        WHERE worker=$1;
    END;
$_$;


ALTER FUNCTION public.answered_task_num(character varying) OWNER TO gabriele;

--
-- Name: answered_task_num(character varying, integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION answered_task_num(character varying, integer) RETURNS TABLE(answered bigint)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task as R join crowdsourcing.task as T ON T.id=R.task 
                        WHERE worker=$1 and T.campaign=$2;
    END;
$_$;


ALTER FUNCTION public.answered_task_num(character varying, integer) OWNER TO gabriele;

--
-- Name: best_task(integer, character varying); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION best_task(integer, character varying) RETURNS TABLE(id integer, title character varying, description character varying)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN
        RETURN QUERY SELECT T.id, T.title, T.description
        FROM crowdsourcing.task AS T 
                JOIN crowdsourcing.campaign as C ON T.campaign=C.id
                JOIN crowdsourcing.requires_keyword as RK ON T.id=RK.task 
                JOIN crowdsourcing.has_keyword as HK ON RK.keyword=HK.keyword
        WHERE T.campaign=$1 
                AND C.start_date<=CURRENT_DATE AND C.end_date>=CURRENT_DATE
                AND T.valid_bit IS NULL 
                AND RK.keyword IN ( SELECT keyword
                                    FROM crowdsourcing.has_keyword
                                    WHERE worker=$2) AND T.id NOT IN(SELECT task
                                                                    FROM crowdsourcing.recives_task
                                                                    WHERE worker=$2)
        ORDER BY HK.score desc
        LIMIT 1;
    END;
$_$;


ALTER FUNCTION public.best_task(integer, character varying) OWNER TO gabriele;

--
-- Name: camp_tasks(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION camp_tasks(integer) RETURNS TABLE(id integer, description character varying, title character varying, n_workers integer, threshold integer, valid_bit boolean, campaign integer, pay_type character varying, pay_description character varying)
    LANGUAGE plpgsql
    AS $_$
    DECLARE i crowdsourcing.task.id%TYPE;
    BEGIN
        RETURN QUERY SELECT * FROM crowdsourcing.task AS T WHERE T.campaign=$1;
    END
$_$;


ALTER FUNCTION public.camp_tasks(integer) OWNER TO gabriele;

--
-- Name: check_majority(); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION check_majority() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
declare
    num_workers INTEGER;
    worker_requested INTEGER;
    task_threshold INTEGER;
    requested_threshold INTEGER;
    worker_id crowdsourcing.worker.user_name%TYPE;
begin
    -- this put at the moment the valid bit not null to be able to use righy_answer function (it will return NULL otherwise)
    update crowdsourcing.task set valid_bit=true WHERE id=NEW.task;

    select COUNT(*)
    from crowdsourcing.choose as CH 
    where CH.task = NEW.task
    into num_workers;

    select n_workers
    from crowdsourcing.task AS T
    where T.id=NEW.task
    into worker_requested;

    select (num_ans*100)/num_workers
    from right_answer(NEW.task) AS R
    limit 1
    into task_threshold;

    select threshold
    from crowdsourcing.task AS T
    where T.id=NEW.task
    into requested_threshold;



    if((num_workers >= worker_requested) and (task_threshold >= requested_threshold)) then

        --- every work answered correctly update to valid ----
        for worker_id in SELECT worker FROM crowdsourcing.recives_task WHERE task=NEW.task and worker IN(select C.worker 
                                                                                                    from crowdsourcing.choose as C
                                                                                                    where C.answer IN (select right_answer from right_answer(NEW.task)))
        loop 
            UPDATE crowdsourcing.recives_task SET valid_bit_user=TRUE WHERE worker=worker_id and task=NEW.task;
        end loop;
        --- task update to valid ----
        update crowdsourcing.task set valid_bit=TRUE WHERE id=NEW.task;

    elseif(num_workers >= worker_requested) then
        --- task update to not valid ----
        update crowdsourcing.task set valid_bit=FALSE WHERE id=NEW.task;

    else
        -- if num_workers < worker_requested the task valid bit should remain null --
        update crowdsourcing.task set valid_bit=NULL WHERE id=NEW.task;

    end if;
    RETURN NEW;
    
end;
$$;


ALTER FUNCTION public.check_majority() OWNER TO gabriele;

--
-- Name: checkk(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION checkk(task_id integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
declare
	num_workers INTEGER;
    worker_requested INTEGER;
    task_threshold INTEGER;
    requested_threshold INTEGER;
    worker_id crowdsourcing.worker.user_name%TYPE;
begin

	select COUNT(*)
	from crowdsourcing.choose as CH 
	where CH.task = task_id
	into num_workers;

    select n_workers
	from crowdsourcing.task AS T
    where T.id=task_id
	into worker_requested;

    select (num_ans*100)/num_workers
	from right_answer(task_id) AS R
    limit 1
	into task_threshold;

    select threshold
	from crowdsourcing.task AS T
    where T.id=task_id
	into requested_threshold;



---    if((num_workers >= worker_requested) and (task_threshold >= requested_threshold)) then
---
---        --- task update to valid ----
---        update crowdsourcing.task set valid_bit=TRUE WHERE id=NEW.task;
---
---        --- every work answered correctly update to valid ----
---        for worker_id in SELECT worker FROM crowdsourcing.recives_task WHERE task=NEW.task and worker IN(select C.worker 
---                                                                                                    from crowdsourcing.choose as C
---                                                                                                    where C.answer IN (select right_answer from right_answer(NEW.task)))
---        loop 
---            UPDATE crowdsourcing.recives_task SET valid_bit_user=TRUE WHERE worker=worker_id and task=NEW.task;
---        end loop;
---
---    elseif(num_workers >= worker_requested) then
---        --- task update to not valid ----
---        update crowdsourcing.task set valid_bit=FALSE WHERE id=NEW.task;
---
---    end if;
---    RETURN NEW;
    RETURN task_threshold;
    
end;
$$;


ALTER FUNCTION public.checkk(task_id integer) OWNER TO gabriele;

--
-- Name: completed_percentage(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION completed_percentage(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
    DECLARE 
        total_task INTEGER;
        completed_task INTEGER; 
    BEGIN
        SELECT count(*)
        FROM crowdsourcing.task AS T
        WHERE T.campaign=$1
        INTO total_task;

        SELECT count(*)
        FROM crowdsourcing.task AS T
        WHERE T.campaign=$1 AND T.id IN (SELECT * FROM completed_task($1))
        INTO completed_task;

        RETURN (completed_task*100)/total_task ;
    END
$_$;


ALTER FUNCTION public.completed_percentage(integer) OWNER TO gabriele;

--
-- Name: completed_task(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION completed_task(integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $_$
    DECLARE 
    BEGIN
        RETURN query SELECT id FROM crowdsourcing.task WHERE valid_bit IS NOT NULL AND campaign=$1;  
    END
$_$;


ALTER FUNCTION public.completed_task(integer) OWNER TO gabriele;

--
-- Name: correct_task_num(character varying); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION correct_task_num(character varying) RETURNS TABLE(correct bigint)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task 
                        WHERE worker=$1 and valid_bit_user=true;
    END;
$_$;


ALTER FUNCTION public.correct_task_num(character varying) OWNER TO gabriele;

--
-- Name: correct_task_num(character varying, integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION correct_task_num(character varying, integer) RETURNS TABLE(correct bigint)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task as R join crowdsourcing.task as T ON T.id=R.task
                        WHERE worker=$1 and T.campaign=$2 and valid_bit_user=true;
    END;
$_$;


ALTER FUNCTION public.correct_task_num(character varying, integer) OWNER TO gabriele;

--
-- Name: right_answer(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION right_answer(task_id integer) RETURNS TABLE(right_answer character varying, num_ans bigint)
    LANGUAGE plpgsql
    AS $$
    DECLARE
        valid BOOLEAN;
        nchar VARCHAR(100); -- used for return a NULL varchar
        nnum BIGINT;-- used for return a NULL bigint
    BEGIN

        nchar:=NULL;
        nnum:=NULL;

        select valid_bit
        from crowdsourcing.task AS T
        where T.id = task_id
        into valid;

        if(valid=true) THEN
            RETURN QUERY    SELECT answer, count(all answer) as num
                            FROM crowdsourcing.choose
                            WHERE task = task_id 
                            GROUP BY answer
                            having count(all answer) >= ALL(SELECT count(all answer) as num
                                                            FROM crowdsourcing.choose
                                                            WHERE task = task_id 
                                                            GROUP BY answer)
                            ORDER BY num DESC;
        else
            RETURN QUERY SELECT nchar, nnum;
        end if;
    END;
$$;


ALTER FUNCTION public.right_answer(task_id integer) OWNER TO gabriele;

--
-- Name: score_update(); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION score_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    DECLARE 
    BEGIN
        if(NEW.valid_bit=TRUE) then
        UPDATE crowdsourcing.joins_campaign SET score = score+1
        WHERE campaign=NEW.campaign and worker IN ( SELECT R.worker
                                                    FROM crowdsourcing.recives_task AS R 
                                                    WHERE R.task=NEW.id and R.valid_bit_user=true
                                                  );
        end if;
        RETURN NEW;
    END;
$$;


ALTER FUNCTION public.score_update() OWNER TO gabriele;

--
-- Name: standing_position(integer, character varying); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION standing_position(integer, character varying) RETURNS TABLE(pos bigint)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
        score_ INTEGER;
    BEGIN
        SELECT score
        FROM crowdsourcing.joins_campaign
        WHERE worker=$2 and campaign=$1
        INTO score_;


        RETURN query    SELECT COUNT(*)+1 as position
                        FROM crowdsourcing.joins_campaign as J
                        WHERE J.campaign=$1 and J.worker IN(SELECT K.worker
                                                            FROM crowdsourcing.joins_campaign as K
                                                            WHERE K.score>score_);
    END;
$_$;


ALTER FUNCTION public.standing_position(integer, character varying) OWNER TO gabriele;

--
-- Name: task_result(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION task_result(integer) RETURNS TABLE(task integer, answer character varying)
    LANGUAGE plpgsql
    AS $_$
    DECLARE tsk crowdsourcing.task.id%TYPE;
    BEGIN
        FOR tsk IN SELECT id FROM crowdsourcing.task as T WHERE T.campaign=$1
        LOOP
            RETURN QUERY SELECT tsk, A.right_answer FROM right_answer(tsk) as A;
        END LOOP;
        RETURN;
    END
$_$;


ALTER FUNCTION public.task_result(integer) OWNER TO gabriele;

--
-- Name: top10(integer); Type: FUNCTION; Schema: public; Owner: gabriele
--

CREATE FUNCTION top10(integer) RETURNS TABLE(worker character varying, score integer)
    LANGUAGE plpgsql
    AS $_$
    DECLARE
    BEGIN 
        RETURN query    SELECT J.worker, J.score
                        FROM crowdsourcing.joins_campaign as J
                        WHERE J.campaign=$1
                        ORDER BY J.score DESC
                        LIMIT 10;
    END;
$_$;


ALTER FUNCTION public.top10(integer) OWNER TO gabriele;

SET search_path = crowdsourcing, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: answer; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE answer (
    task integer NOT NULL,
    value character varying(100) NOT NULL
);


ALTER TABLE answer OWNER TO gabriele;

--
-- Name: campaign; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE campaign (
    id integer NOT NULL,
    registration_start_date date NOT NULL,
    registration_end_date date NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL,
    requester character varying(20) NOT NULL,
    name character varying(20) NOT NULL,
    CONSTRAINT campaign_check CHECK ((registration_end_date > registration_start_date)),
    CONSTRAINT campaign_check1 CHECK ((end_date > start_date)),
    CONSTRAINT campaign_end_date_check CHECK ((end_date > CURRENT_DATE)),
    CONSTRAINT campaign_registration_end_date_check CHECK ((registration_end_date > CURRENT_DATE)),
    CONSTRAINT campaign_registration_start_date_check CHECK ((registration_start_date >= CURRENT_DATE)),
    CONSTRAINT campaign_start_date_check CHECK ((start_date >= CURRENT_DATE))
);


ALTER TABLE campaign OWNER TO gabriele;

--
-- Name: campaign_id_seq; Type: SEQUENCE; Schema: crowdsourcing; Owner: gabriele
--

CREATE SEQUENCE campaign_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE campaign_id_seq OWNER TO gabriele;

--
-- Name: campaign_id_seq; Type: SEQUENCE OWNED BY; Schema: crowdsourcing; Owner: gabriele
--

ALTER SEQUENCE campaign_id_seq OWNED BY campaign.id;


--
-- Name: choose; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE choose (
    worker character varying(20) NOT NULL,
    task integer NOT NULL,
    answer character varying(100) NOT NULL
);


ALTER TABLE choose OWNER TO gabriele;

--
-- Name: has_keyword; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE has_keyword (
    worker character varying(20) NOT NULL,
    keyword character varying(50) NOT NULL,
    score integer DEFAULT 0 NOT NULL,
    CONSTRAINT has_keyword_score_check CHECK ((score >= 0))
);


ALTER TABLE has_keyword OWNER TO gabriele;

--
-- Name: joins_campaign; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE joins_campaign (
    worker character varying(20) NOT NULL,
    campaign integer NOT NULL,
    score integer DEFAULT 0 NOT NULL,
    CONSTRAINT joins_campaign_score_check CHECK ((score >= 0))
);


ALTER TABLE joins_campaign OWNER TO gabriele;

--
-- Name: keyword; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE keyword (
    keyword character varying(50) NOT NULL,
    type keyword_type NOT NULL
);


ALTER TABLE keyword OWNER TO gabriele;

--
-- Name: pay; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE pay (
    type pay_type NOT NULL
);


ALTER TABLE pay OWNER TO gabriele;

--
-- Name: recives_task; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE recives_task (
    task integer NOT NULL,
    worker character varying(20) NOT NULL,
    valid_bit_user boolean DEFAULT false NOT NULL
);


ALTER TABLE recives_task OWNER TO gabriele;

--
-- Name: requester; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE requester (
    user_name character varying(20) NOT NULL,
    password character varying(20) NOT NULL,
    name character varying(20),
    surname character varying(20),
    accepted boolean DEFAULT false NOT NULL,
    CONSTRAINT requester_password_check CHECK (((password)::text <> ''::text)),
    CONSTRAINT requester_user_name_check CHECK (((user_name)::text <> ''::text))
);


ALTER TABLE requester OWNER TO gabriele;

--
-- Name: requires_keyword; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE requires_keyword (
    task integer NOT NULL,
    keyword character varying(50) NOT NULL
);


ALTER TABLE requires_keyword OWNER TO gabriele;

--
-- Name: task; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE task (
    id integer NOT NULL,
    description character varying(700),
    title character varying(50) NOT NULL,
    n_workers integer NOT NULL,
    threshold integer NOT NULL,
    valid_bit boolean,
    campaign integer NOT NULL,
    pay_type character varying(50) NOT NULL,
    pay_description character varying(280) NOT NULL,
    CONSTRAINT task_n_workers_check CHECK ((n_workers > 0)),
    CONSTRAINT task_threshold_check CHECK ((threshold > 0))
);


ALTER TABLE task OWNER TO gabriele;

--
-- Name: task_id_seq; Type: SEQUENCE; Schema: crowdsourcing; Owner: gabriele
--

CREATE SEQUENCE task_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE task_id_seq OWNER TO gabriele;

--
-- Name: task_id_seq; Type: SEQUENCE OWNED BY; Schema: crowdsourcing; Owner: gabriele
--

ALTER SEQUENCE task_id_seq OWNED BY task.id;


--
-- Name: worker; Type: TABLE; Schema: crowdsourcing; Owner: gabriele
--

CREATE TABLE worker (
    user_name character varying(20) NOT NULL,
    password character varying(20) NOT NULL,
    name character varying(20),
    surname character varying(20),
    CONSTRAINT worker_password_check CHECK (((password)::text <> ''::text)),
    CONSTRAINT worker_user_name_check CHECK (((user_name)::text <> ''::text))
);


ALTER TABLE worker OWNER TO gabriele;

--
-- Name: campaign id; Type: DEFAULT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY campaign ALTER COLUMN id SET DEFAULT nextval('campaign_id_seq'::regclass);


--
-- Name: task id; Type: DEFAULT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY task ALTER COLUMN id SET DEFAULT nextval('task_id_seq'::regclass);


--
-- Data for Name: answer; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY answer (task, value) FROM stdin;
43	Bianco
43	Nero
43	Non andava a cavallo
48	a
48	b
49	a
49	b
50	Molto bene
50	Bene
50	Indifferente
50	Male
50	Mi disturba
51	I agree
51	I do not agree
52	yes
52	no
53	Jon Anderson
53	Ahmad Alaadeen
54	Audi
54	BMW
54	Mercedes
55	yes
55	no
56	yes
56	no
57	IntelliJ
57	Eclipse
58	Programmazione funzionale
58	Programmazione concorrente
58	Programmazione ad oggetti
59	si
59	no
60	Inter
60	Milan
61	Scherma
61	Ginnastica artistica
61	Nuoto
61	Rugby
62	0
62	1-2 volte
62	3-4 volte
62	5 volte
62	ogni giorno
\.


--
-- Data for Name: campaign; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY campaign (id, registration_start_date, registration_end_date, start_date, end_date, requester, name) FROM stdin;
18	2019-01-03	2019-01-06	2019-01-03	2019-02-16	req1	Programmazione
20	2019-01-03	2019-01-04	2019-01-04	2019-01-05	req2	Programmazione
25	2019-01-03	2019-01-04	2019-01-03	2019-01-04	req2	Scaduta
39	2019-01-04	2019-02-02	2019-01-09	2019-03-02	req3	Is it Spam?
28	2019-01-04	2019-01-05	2019-01-04	2019-03-16	req3	Musica
40	2019-01-12	2019-03-30	2019-01-12	2019-06-28	req2	Automobili
41	2019-03-20	2019-03-31	2019-03-21	2019-06-14	req1	Sport
\.


--
-- Data for Name: choose; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY choose (worker, task, answer) FROM stdin;
lav2	43	Bianco
lav2	48	a
lav1	49	b
lav1	43	Bianco
lav3	48	b
lav4	48	a
lav1	50	Bene
lav2	50	Molto bene
lav3	50	Molto bene
lav3	51	I agree
lav1	51	I agree
lav2	51	I do not agree
lav4	51	I agree
lav1	54	Audi
lav2	54	BMW
lav5	54	Mercedes
\.


--
-- Data for Name: has_keyword; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY has_keyword (worker, keyword, score) FROM stdin;
lav1	italian	8
lav2	italian	7
lav2	english	10
lav2	fast reading	6
lav3	english	5
lav4	english	3
lav3	musica classica	4
lav1	rock	10
lav4	U2	6
lav5	italian	1
\.


--
-- Data for Name: joins_campaign; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY joins_campaign (worker, campaign, score) FROM stdin;
lav2	25	0
lav1	25	1
lav3	25	0
lav4	25	0
lav1	18	0
lav2	28	1
lav1	28	1
lav4	28	1
lav3	28	2
lav2	39	0
lav1	40	0
lav2	40	0
lav5	40	0
\.


--
-- Data for Name: keyword; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY keyword (keyword, type) FROM stdin;
milan	knowledge
sport	knowledge
soccer	knowledge
italian	knowledge
english	knowledge
fast reading	attitude
music	knowledge
musica classica	knowledge
U2	knowledge
rock	knowledge
youtube	knowledge
spam	knowledge
	knowledge
jazz	knowledge
auto	knowledge
spotify	knowledge
smartphone	knowledge
computer science	knowledge
java	knowledge
programming	knowledge
html	knowledge
\.


--
-- Data for Name: pay; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY pay (type) FROM stdin;
coupon
freeItem
money
promotionalCode
\.


--
-- Data for Name: recives_task; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY recives_task (task, worker, valid_bit_user) FROM stdin;
48	lav2	f
49	lav1	f
43	lav2	t
43	lav1	t
48	lav3	f
48	lav4	f
50	lav1	f
50	lav2	t
50	lav3	t
51	lav2	f
51	lav3	t
51	lav1	t
51	lav4	t
54	lav1	f
54	lav2	f
54	lav5	f
\.


--
-- Data for Name: requester; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY requester (user_name, password, name, surname, accepted) FROM stdin;
req4	pas	Requester	4	f
req5	pas	Requester	5	f
admin	admin	admin	admin	t
req1	pas	Requester	1	t
req3	pas	Requester	3	t
req2	pas	Requester	2	t
\.


--
-- Data for Name: requires_keyword; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY requires_keyword (task, keyword) FROM stdin;
48	english
48	fast reading
49	italian
49	fast reading
50	music
50	italian
50	musica classica
51	music
51	english
51	U2
51	rock
52	english
52	youtube
52	spam
52	
53	jazz
53	english
53	music
54	auto
54	italian
54	
55	english
55	spam
55	spotify
55	music
56	english
56	smartphone
57	italian
57	computer science
57	java
58	italian
58	computer science
58	programming
59	italian
59	html
59	computer science
60	italian
60	milan
60	sport
60	soccer
61	sport
61	italian
62	sport
62	italian
43	italian
\.


--
-- Data for Name: task; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY task (id, description, title, n_workers, threshold, valid_bit, campaign, pay_type, pay_description) FROM stdin;
52	"Italy earthquake: More than 15,000 people in shelters! https://www.youtube.com/watch?v=8B1ioB8OykQ" is this tweet a spam?	Youtube tweet	3	70	\N	39	money	1$
54	Scegliere quale marca di automobili preferite	È meglio...	3	70	f	40	coupon	viaggio alle maldive
55	"Register today to obtain 3 month free" is this a spam?	Spotify	3	75	\N	39	money	$3
57	Quale IDE preferisci?	Java	3	70	\N	18	promotionalCode	ndnw8e289392rub3duW7BDAD
48	English task	English	3	70	f	25	money	3$
59	HTML è un linguaggio di programmazione?	HTML	2	60	\N	18	promotionalCode	92jr0028c3hrc2er23
60	Quale squadra preferisci?	Calcio di Milano	3	60	\N	41	money	0.1$
62	Quanto sport pratichi durante la tua settimana?	Attività fisica	4	60	\N	41	money	0.1$
50	Per me la musica classica è la musica ideale quando sono in cerca di ispirazioni. In particolare trovo i suoni solenni e svettanti davvero stimolanti. I miei preferiti sono Beethoven e Bach. Questa frase ti fa sentire?	Musica classica	3	60	t	28	promotionalCode	898238729376566832
51	The rock band U2 is good,  but not great concert in San Jose	U2	4	70	t	28	money	0.1$
53	"One of the great moments of my life was when I could write musician on my passport." Who said this?	Jazz quotations	3	70	\N	28	promotionalCode	oi3e2o8e2h3e7263evdwjdnwo
56	"The new flagship is amazing" is this a spam?	Smartphone	4	60	\N	39	freeItem	1 smartphone
58	Quale paradigma preferisci?	Paradigma	3	70	\N	18	promotionalCode	823dh898dh9wh392h38
61	Quale sport ritieni sia più affascinante?	Preferenze	3	70	\N	41	money	0.1$
49	Task in italiano	Italiano	3	70	\N	25	money	3$
43	Questa è la descrizione del task. Di che colore è il cavallo bianco di Napoleone?	Prova	2	60	t	25	coupon	2
\.


--
-- Data for Name: worker; Type: TABLE DATA; Schema: crowdsourcing; Owner: gabriele
--

COPY worker (user_name, password, name, surname) FROM stdin;
lav2	lav	Lavoratore	2
lav3	lav	Lavoratore	3
lav4	pas	Lavoratore	4
lav5	pas	Lavoratore	5
lav1	pas	Lavoratore	1
\.


--
-- Name: campaign_id_seq; Type: SEQUENCE SET; Schema: crowdsourcing; Owner: gabriele
--

SELECT pg_catalog.setval('campaign_id_seq', 41, true);


--
-- Name: task_id_seq; Type: SEQUENCE SET; Schema: crowdsourcing; Owner: gabriele
--

SELECT pg_catalog.setval('task_id_seq', 62, true);


--
-- Name: answer answer_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY answer
    ADD CONSTRAINT answer_pkey PRIMARY KEY (task, value);


--
-- Name: campaign campaign_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY campaign
    ADD CONSTRAINT campaign_pkey PRIMARY KEY (id);


--
-- Name: choose choose_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY choose
    ADD CONSTRAINT choose_pkey PRIMARY KEY (worker, task);


--
-- Name: has_keyword has_keyword_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY has_keyword
    ADD CONSTRAINT has_keyword_pkey PRIMARY KEY (worker, keyword);


--
-- Name: joins_campaign joins_campaign_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY joins_campaign
    ADD CONSTRAINT joins_campaign_pkey PRIMARY KEY (worker, campaign);


--
-- Name: keyword keyword_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY keyword
    ADD CONSTRAINT keyword_pkey PRIMARY KEY (keyword);


--
-- Name: pay pay_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY pay
    ADD CONSTRAINT pay_pkey PRIMARY KEY (type);


--
-- Name: recives_task recives_task_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY recives_task
    ADD CONSTRAINT recives_task_pkey PRIMARY KEY (task, worker);


--
-- Name: requester requester_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY requester
    ADD CONSTRAINT requester_pkey PRIMARY KEY (user_name);


--
-- Name: requires_keyword requires_keyword_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY requires_keyword
    ADD CONSTRAINT requires_keyword_pkey PRIMARY KEY (task, keyword);


--
-- Name: task task_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY task
    ADD CONSTRAINT task_pkey PRIMARY KEY (id);


--
-- Name: task task_title_campaign_key; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY task
    ADD CONSTRAINT task_title_campaign_key UNIQUE (title, campaign);


--
-- Name: worker worker_pkey; Type: CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY worker
    ADD CONSTRAINT worker_pkey PRIMARY KEY (user_name);


--
-- Name: choose answer_insert; Type: TRIGGER; Schema: crowdsourcing; Owner: gabriele
--

CREATE TRIGGER answer_insert AFTER INSERT ON choose FOR EACH ROW EXECUTE PROCEDURE public.check_majority();


--
-- Name: task worker_score_update; Type: TRIGGER; Schema: crowdsourcing; Owner: gabriele
--

CREATE TRIGGER worker_score_update AFTER UPDATE OF valid_bit ON task FOR EACH ROW EXECUTE PROCEDURE public.score_update();


--
-- Name: answer answer_task_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY answer
    ADD CONSTRAINT answer_task_fkey FOREIGN KEY (task) REFERENCES task(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: campaign campaign_requester_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY campaign
    ADD CONSTRAINT campaign_requester_fkey FOREIGN KEY (requester) REFERENCES requester(user_name) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: choose choose_task_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY choose
    ADD CONSTRAINT choose_task_fkey FOREIGN KEY (task, answer) REFERENCES answer(task, value);


--
-- Name: choose choose_task_fkey1; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY choose
    ADD CONSTRAINT choose_task_fkey1 FOREIGN KEY (task) REFERENCES task(id);


--
-- Name: choose choose_worker_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY choose
    ADD CONSTRAINT choose_worker_fkey FOREIGN KEY (worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: has_keyword has_keyword_keyword_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY has_keyword
    ADD CONSTRAINT has_keyword_keyword_fkey FOREIGN KEY (keyword) REFERENCES keyword(keyword) ON UPDATE CASCADE;


--
-- Name: has_keyword has_keyword_worker_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY has_keyword
    ADD CONSTRAINT has_keyword_worker_fkey FOREIGN KEY (worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: joins_campaign joins_campaign_campaign_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY joins_campaign
    ADD CONSTRAINT joins_campaign_campaign_fkey FOREIGN KEY (campaign) REFERENCES campaign(id) ON UPDATE CASCADE;


--
-- Name: joins_campaign joins_campaign_worker_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY joins_campaign
    ADD CONSTRAINT joins_campaign_worker_fkey FOREIGN KEY (worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: recives_task recives_task_task_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY recives_task
    ADD CONSTRAINT recives_task_task_fkey FOREIGN KEY (task) REFERENCES task(id) ON UPDATE CASCADE;


--
-- Name: recives_task recives_task_worker_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY recives_task
    ADD CONSTRAINT recives_task_worker_fkey FOREIGN KEY (worker) REFERENCES worker(user_name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: requires_keyword requires_keyword_keyword_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY requires_keyword
    ADD CONSTRAINT requires_keyword_keyword_fkey FOREIGN KEY (keyword) REFERENCES keyword(keyword) ON UPDATE CASCADE;


--
-- Name: requires_keyword requires_keyword_task_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY requires_keyword
    ADD CONSTRAINT requires_keyword_task_fkey FOREIGN KEY (task) REFERENCES task(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: task task_campaign_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY task
    ADD CONSTRAINT task_campaign_fkey FOREIGN KEY (campaign) REFERENCES campaign(id);


--
-- Name: task task_pay_type_fkey; Type: FK CONSTRAINT; Schema: crowdsourcing; Owner: gabriele
--

ALTER TABLE ONLY task
    ADD CONSTRAINT task_pay_type_fkey FOREIGN KEY (pay_type) REFERENCES pay(type);


--
-- Name: crowdsourcing; Type: ACL; Schema: -; Owner: gabriele
--

GRANT ALL ON SCHEMA crowdsourcing TO admin;


--
-- Name: answer; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE answer TO admin;


--
-- Name: campaign; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE campaign TO admin;


--
-- Name: campaign_id_seq; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON SEQUENCE campaign_id_seq TO admin;


--
-- Name: choose; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE choose TO admin;


--
-- Name: has_keyword; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE has_keyword TO admin;


--
-- Name: joins_campaign; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE joins_campaign TO admin;


--
-- Name: keyword; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE keyword TO admin;


--
-- Name: pay; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE pay TO admin;


--
-- Name: recives_task; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE recives_task TO admin;


--
-- Name: requester; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE requester TO admin;


--
-- Name: requires_keyword; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE requires_keyword TO admin;


--
-- Name: task; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE task TO admin;


--
-- Name: task_id_seq; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON SEQUENCE task_id_seq TO admin;


--
-- Name: worker; Type: ACL; Schema: crowdsourcing; Owner: gabriele
--

GRANT ALL ON TABLE worker TO admin;


--
-- PostgreSQL database dump complete
--



------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function check if a task reached the requested majority ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE TRIGGER answer_insert after insert on crowdsourcing.choose
for each row
	execute procedure check_majority();
	

CREATE OR REPLACE FUNCTION check_majority()
RETURNS TRIGGER as $$
declare
	num_workers INTEGER;
    worker_requested INTEGER;
    task_threshold INTEGER;
    requested_threshold INTEGER;
    worker_id crowdsourcing.worker.user_name%TYPE;
begin
    -- this put at the moment the valid bit not null to be able to use righy_answer function (it will return NULL otherwise)
    update crowdsourcing.task set valid_bit=FALSE WHERE id=NEW.task;

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

        --- task update to valid ----
        update crowdsourcing.task set valid_bit=TRUE WHERE id=NEW.task;

        --- every work answered correctly update to valid ----
        for worker_id in SELECT worker FROM crowdsourcing.recives_task WHERE task=NEW.task and worker IN(select C.worker 
                                                                                                    from crowdsourcing.choose as C
                                                                                                    where C.answer IN (select right_answer from right_answer(NEW.task)))
        loop 
            UPDATE crowdsourcing.recives_task SET valid_bit_user=TRUE WHERE worker=worker_id and task=NEW.task;
        end loop;

    elseif(num_workers >= worker_requested) then
        --- task update to not valid ----
        update crowdsourcing.task set valid_bit=FALSE WHERE id=NEW.task;

    else
        -- if num_workers < worker_requested the task valid bit should remain null --
        update crowdsourcing.task set valid_bit=NULL WHERE id=NEW.task;

    end if;
    RETURN NEW;
    
end;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This trigger updates the workers scores ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE TRIGGER worker_score_update AFTER UPDATE of valid_bit ON crowdsourcing.task
FOR EACH ROW
    execute procedure score_update();


CREATE OR REPLACE FUNCTION score_update()
RETURNS TRIGGER AS $$
    DECLARE 
    BEGIN
        if(NEW.valid_bit=true) then
        UPDATE crowdsourcing.joins_campaign SET score = score+1
        WHERE campaign=NEW.campaign and worker IN ( SELECT R.worker
                                                    FROM crowdsourcing.recives_task AS R 
                                                    WHERE R.task=NEW.id and R.valid_bit_user=true
                                                  );
        end if;
        RETURN NEW;
    END;
$$ language plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function find wich answer get more hits for task and returns its value (or values if we have a tie) ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION right_answer(task_id INTEGER)
RETURNS TABLE(right_answer VARCHAR(100), num_ans BIGINT) AS $$
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
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function assign the best task to a worker ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION best_task (INTEGER, varchar(20)) 
RETURNS TABLE(id INTEGER, title VARCHAR(50), description VARCHAR(280)) AS $$
    DECLARE
    BEGIN
        RETURN QUERY SELECT T.id, T.title, T.description
        FROM crowdsourcing.task AS T JOIN crowdsourcing.requires_keyword as RK ON T.id=RK.task join crowdsourcing.has_keyword as HK on RK.keyword=HK.keyword
        WHERE T.campaign=$1 AND T.valid_bit IS NULL AND RK.keyword IN (  SELECT keyword
                                                                    FROM crowdsourcing.has_keyword
                                                                    WHERE worker=$2) AND T.id NOT IN(SELECT task
                                                                                                    FROM crowdsourcing.recives_task
                                                                                                    WHERE worker=$2)
        ORDER BY HK.score desc
        LIMIT 1;
    END;
$$ LANGUAGE plpgsql;






------------------------------------------------------------------------------------------------------------------------------------------------------------
---- CAMPAIGN REPORT ------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------

--- this function shows task contained in a campaign ----

CREATE OR REPLACE FUNCTION camp_tasks (INTEGER) --integer: campaign 
RETURNS TABLE(id INTEGER, description crowdsourcing.task.description%TYPE, title crowdsourcing.task.title%TYPE, n_workers crowdsourcing.task.n_workers%TYPE, threshold crowdsourcing.task.threshold%TYPE, valid_bit crowdsourcing.task.valid_bit%TYPE, campaign crowdsourcing.task.campaign%TYPE, pay_type crowdsourcing.task.pay_type%TYPE, pay_description crowdsourcing.task.pay_description%TYPE) as $$
    DECLARE i crowdsourcing.task.id%TYPE;
    BEGIN
        RETURN QUERY SELECT * FROM crowdsourcing.task AS T WHERE T.campaign=$1;
    END
$$ LANGUAGE plpgsql;

--- this function shows results for each task of a campaign ---

CREATE OR REPLACE FUNCTION task_result (INTEGER) --integer: campaign
RETURNS TABLE(task INTEGER, answer VARCHAR(100))  as $$
    DECLARE tsk crowdsourcing.task.id%TYPE;
    BEGIN
        FOR tsk IN SELECT id FROM crowdsourcing.task as T WHERE T.campaign=$1
        LOOP
            RETURN QUERY SELECT tsk, A.right_answer FROM right_answer(tsk) as A;
        END LOOP;
        RETURN;
    END
$$ LANGUAGE plpgsql;

--- this function return task of a campaign that are completed ---

CREATE OR REPLACE FUNCTION completed_task(INTEGER) --integer: campaign
RETURNS SETOF INTEGER AS $$
    DECLARE 
    BEGIN
        RETURN query SELECT id FROM crowdsourcing.task WHERE valid_bit IS NOT NULL AND campaign=$1;  
    END
$$ LANGUAGE plpgsql;

--- this function returns the percentage of completed task ---

CREATE OR REPLACE FUNCTION completed_percentage(INTEGER) --integer: campaign
RETURNS INTEGER AS $$
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
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function returns the top 10 worker for a campaign ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION top10(crowdsourcing.campaign.id%TYPE)
RETURNS TABLE(worker crowdsourcing.joins_campaign.worker%TYPE, score crowdsourcing.joins_campaign.score%TYPE) as $$
    DECLARE
    BEGIN 
        RETURN query    SELECT J.worker, J.score
                        FROM crowdsourcing.joins_campaign as J
                        WHERE J.campaign=$1
                        ORDER BY J.score DESC
                        LIMIT 10;
    END;
$$ LANGUAGE plpgsql;

------------------------------------------------------------------------------------------------------------------------------------------------------------
---- WORKER STATISTICS ------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------

---- this function returns standing the position of a worker in a defined campaign
CREATE OR REPLACE FUNCTION standing_position(crowdsourcing.campaign.id%TYPE, crowdsourcing.worker.user_name%TYPE)
RETURNS table(pos BIGINT) AS $$
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
$$ LANGUAGE plpgsql;


---- this function returns number of task answered by a worker  in a campaign
CREATE OR REPLACE FUNCTION answered_task_num(crowdsourcing.worker.user_name%TYPE, crowdsourcing.campaign.id%TYPE)
RETURNS table(answered BIGINT) as $$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task as R join crowdsourcing.task as T ON T.id=R.task 
                        WHERE worker=$1 and T.campaign=$2;
    END;
$$ LANGUAGE plpgsql;

---- this function returns number of task answered correctly by a worker in a campaign
CREATE OR REPLACE FUNCTION correct_task_num(crowdsourcing.worker.user_name%TYPE, crowdsourcing.campaign.id%TYPE)
RETURNS table(correct BIGINT) as $$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task as R join crowdsourcing.task as T ON T.id=R.task
                        WHERE worker=$1 and T.campaign=$2 and valid_bit_user=true;
    END;
$$ LANGUAGE plpgsql;
------------------------------------------------------------------------------------------------------------------------------------------------------------
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------

---- this function returns number of task answered by a worker 
CREATE OR REPLACE FUNCTION answered_task_num(crowdsourcing.worker.user_name%TYPE)
RETURNS table(answered BIGINT) as $$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task  
                        WHERE worker=$1;
    END;
$$ LANGUAGE plpgsql;

---- this function returns number of task answered correctly by a worker
CREATE OR REPLACE FUNCTION correct_task_num(crowdsourcing.worker.user_name%TYPE)
RETURNS table(correct BIGINT) as $$
    DECLARE
    BEGIN
        RETURN query    SELECT COUNT(*)
                        FROM crowdsourcing.recives_task 
                        WHERE worker=$1 and valid_bit_user=true;
    END;
$$ LANGUAGE plpgsql;
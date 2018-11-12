
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

    end if;
    RETURN NEW;
    
end;
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function find wich answer get more hits for task and returns its value (or values if we have a tie) ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION right_answer(task_id INTEGER)
RETURNS TABLE(right_answer VARCHAR(100), num_ans BIGINT) AS $$
    DECLARE
    BEGIN
   
        RETURN QUERY    SELECT answer, count(all answer) as num
                        FROM crowdsourcing.choose
                        WHERE task = task_id 
                        GROUP BY answer
                        having count(all answer) >= ALL(SELECT count(all answer) as num
                                                        FROM crowdsourcing.choose
                                                        WHERE task = task_id 
                                                        GROUP BY answer)
                        ORDER BY num DESC;
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
        WHERE T.campaign=$1 AND RK.keyword IN (SELECT keyword
                                            FROM crowdsourcing.has_keyword
                                            WHERE worker=$2) AND T.id NOT IN(SELECT task
                                                                            FROM crowdsourcing.recives_task
                                                                            WHERE worker=$2)
        LIMIT 1;
    END;
$$ LANGUAGE plpgsql;






------------------------------------------------------------------------------------------------------------------------------------------------------------
---- CAMPAIGN REPORT ------------------------------------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------------------------------------

--- this function shows task contained in a campaign ----

CREATE OR REPLACE FUNCTION camp_tasks (INTEGER)
RETURNS SETOF INTEGER as $$
    DECLARE i crowdsourcing.task.id%TYPE;
    BEGIN
        FOR i IN SELECT id FROM crowdsourcing.task AS T WHERE T.campaign=$1
        LOOP
            RETURN NEXT i;
        END LOOP; 
        RETURN;
    END
$$ LANGUAGE plpgsql;

--- this function shows results for each task of a campaign ---

CREATE OR REPLACE FUNCTION task_result (INTEGER)
RETURNS TABLE(task INTEGER, answer VARCHAR(100))  as $$
    DECLARE tsk crowdsourcing.task.id%TYPE;
    BEGIN
        FOR tsk IN SELECT id FROM crowdsourcing.task as T WHERE T.campaign=$1
        LOOP
            RETURN QUERY SELECT tsk, A.right_answer FROM right_answer(tsk) as A;
        END LOOP;
        RETURN;
    END
$$ LANGUAGE plpgsql

--- this function return task of a campaign that are completed ---

CREATE OR REPLACE FUNCTION completed_task(INTEGER) 
RETURNS SETOF INTEGER AS $$
    DECLARE 
    BEGIN
        RETURN query SELECT id FROM crowdsourcing.task WHERE valid_bit=true  
    END
$$ LANGUAGE plpgsql
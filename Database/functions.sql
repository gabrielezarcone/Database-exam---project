
------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function check if a task reached the requested majority ------------------------------------------------------------------------------------------------------------------------------------------------------------

REATE TRIGGER answer_insert after insert on crowdsourcing.choose
for each row
	execute procedure check_majority();
	

CREATE OR REPLACE FUNCTION check_majority()
RETURNS TRIGGER as $$
declare
	num_workers INTEGER;
    threshold INTEGER;
begin

	select COUNT(*)
	from crowdsourcing.choose as CH 
	where CH.task = NEW.task
	into num_workers;

    select threshold
	from crowdsourcing.task AS T
	into threshold;


    if(num_workers >= threshold){
        update crowdsourcing.task set valid_bit=TRUE WHERE id=NEW.id;

        for worker_id in SELECT * FROM crowdsourcing.recives_task WHERE task=NEW.task and  
    }




	if(not(correct_answer = -1)) then
				
		for worker_id in select worker from executes where answer = correct_answer
		loop
		
			update joins set score = (score + 1) where worker = worker_id and campaign = work_campaign_id;
			temp = new.exe_time;
			update joins set last_correct_submission = temp where worker = worker_id and campaign = work_campaign_id;
		
		end loop;
	
	end if;
	
	return new;

end;
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function find wich answer get more hits for task and returns its value (or values if we have a tie) or 'NO_MAJORITY' if there's no majority yet ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION right_answer(task_id INTEGER)
RETURNS SETOF VARCHAR(100) AS $$
    DECLARE
        majority BOOLEAN;
        ans crowdsourcing.choose.answer%TYPE;
    BEGIN
        SELECT valid_bit
        FROM crowdsourcing.task
        WHERE id=task_id
        INTO majority;

        if(majority) then

            for ans in  SELECT answer, count(all answer) as num
                        FROM crowdsourcing.choose
                        WHERE task = task_id 
                        GROUP BY answer
                        having count(all answer) >= ALL(SELECT count(all answer) as num
                                                        FROM crowdsourcing.choose
                                                        WHERE task = 6 
                                                        GROUP BY answer)
                        ORDER BY num DESC
            loop 
                RETURN NEXT ans;
            end loop;
            RETURN;

        else
            RETURN NEXT 'NO_MAJORITY';
        end if;

    END;
$$ language plpgsql;


------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function assign the best task to a worker ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION best_task (INTEGER, varchar(20)) RETURNS TABLE(id INTEGER, title VARCHAR(50), description VARCHAR(280)) AS $$
    DECLARE
    BEGIN
        RETURN QUERY SELECT T.id, T.title, T.description
        FROM crowdsourcing.task AS T JOIN crowdsourcing.requires_keyword as RK ON T.id=RK.task join crowdsourcing.has_keyword as HK on RK.keyword=HK.keyword
        WHERE T.campaign=$1 AND RK.keyword IN (SELECT keyword
                                            FROM crowdsourcing.has_keyword
                                            WHERE worker=$2) AND T.id NOT IN(SELECT task
                                                                            FROM crowdsourcing.recives_task
                                                                            WHERE worker=$2)
        order by HK.score desc
        LIMIT 1;
    END;
$$ LANGUAGE plpgsql;




------------------------------------------------------------------------------------------------------------------------------------------------------------
---- This function create a campaign report ------------------------------------------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION campaign_report (id INTEGER) RETURNS TABLE(id INTEGER, title VARCHAR(50), description VARCHAR(280)) AS $$
    DECLARE
    BEGIN
        RETURN QUERY SELECT T.id, T.title, T.description
        FROM crowdsourcing.task AS T JOIN crowdsourcing.requires_keyword as RK ON T.id=RK.task join crowdsourcing.has_keyword as HK on RK.keyword=HK.keyword
        WHERE T.campaign=$1 AND RK.keyword IN (SELECT keyword
                                            FROM crowdsourcing.has_keyword
                                            WHERE worker=$2) AND T.id NOT IN(SELECT task
                                                                            FROM crowdsourcing.recives_task
                                                                            WHERE worker=$2)
        order by HK.score desc
        LIMIT 1;
    END;
$$ LANGUAGE plpgsql;
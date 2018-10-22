



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
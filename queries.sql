
DELIMITER $$
CREATE FUNCTION get_year_total_expensed_amount(yid VARCHAR(255), vid VARCHAR(255))
RETURNS DECIMAL(18,2)
DETERMINISTIC
BEGIN

	DECLARE sdate DATE;
    DECLARE edate DATE;
    DECLARE total_expensed DECIMAL(18,2);
    
    SET sdate = (SELECT start_date FROM years WHERE id COLLATE utf8mb4_unicode_ci = yid);
	SET edate = (SELECT end_date FROM years WHERE id COLLATE utf8mb4_unicode_ci = yid);
    
    SET total_expensed = (SELECT COALESCE(SUM(e.amount),0) AS expensed_amount
                      FROM 
                     	expenses e
                      WHERE	
                     	(e.date BETWEEN sdate AND edate)
                         AND (e.votehead_id COLLATE utf8mb4_unicode_ci = vid) 
                      	AND (e.is_deleted = 0)
                     );
    
    RETURN total_expensed;
END $$
DELIMITER ;

DELIMITER $$
CREATE FUNCTION get_budgeted_amount(yid VARCHAR(255), vid VARCHAR(255))
RETURNS DECIMAL(18,2)
DETERMINISTIC
BEGIN

    DECLARE budgeted_amount DECIMAL(18,2);    
	SET budgeted_amount = (SELECT COALESCE(SUM(d.amount),0) AS expensed_amount
                      FROM 
                     	budget_details d join budget_headers h on d.header_id = h.id
                      WHERE	
                     	(h.year_id COLLATE utf8mb4_unicode_ci = yid) 
                         AND (d.votehead_id COLLATE utf8mb4_unicode_ci = vid) 
                     );
    
    RETURN budgeted_amount;
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_budget_expense(IN yid VARCHAR(255))
BEGIN
	SELECT
    	v.name,
        get_budgeted_amount(yid,v.id) AS budgeted_amount,
        get_year_total_expensed_amount(yid,v.id) AS expensed_amount
    FROM
    	vote_heads v
    WHERE
    	v.voteheadType = 'EXPENSE' 
    ORDER BY v.name;
END$$
DELIMITER ;
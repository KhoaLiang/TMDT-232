use bookstore;

-- **** ID generator ****
drop trigger if exists authorInsertUltilityTrigger;
delimiter //
create trigger authorInsertUltilityTrigger
before insert on author
for each row
begin
    declare newIndex int default 0;
    select authorIdx into newIndex from author where bookID=new.bookID order by authorIdx desc limit 1;
    set newIndex=newIndex+1;
    set new.authorIdx=newIndex;
end//
delimiter ;

drop trigger if exists categoryInsertUltilityTrigger;
delimiter //
create trigger categoryInsertUltilityTrigger
before insert on category
for each row
begin
    declare counter int default 0;
    select cast(substr(id,9) as unsigned) into counter from category ORDER BY cast(substr(id,9) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    set new.id=concat('CATEGORY',counter);
end//
delimiter ;

-- ********************************************************************************************
-- ********************************************************************************************
-- ********************************************************************************************
-- ********************************************************************************************

-- **** Derived properties calculator ****

-- Calculate book's average rating after insert, update, delete opeartions on the `rating` table
drop trigger if exists avgRatingAfterInsertTrigger;
DELIMITER //
CREATE TRIGGER avgRatingAfterInsertTrigger
AFTER insert ON rating
FOR EACH ROW
BEGIN
    DECLARE totalStar double default 0;
    DECLARE totalRating int default 0;
    DECLARE newAverageRating double default 0;

    SELECT COUNT(*), SUM(star) INTO totalRating, totalStar FROM rating WHERE rating.bookID = NEW.bookID;
    
    IF totalRating > 0 THEN
		SET newAverageRating := totalStar / totalRating;
    END IF;
    UPDATE book SET avgRating = newAverageRating WHERE book.id=new.bookID;
END//
DELIMITER ;

drop trigger if exists avgRatingAfterUpdateTrigger;
DELIMITER //
CREATE TRIGGER avgRatingAfterUpdateTrigger
AFTER update ON rating
FOR EACH ROW
BEGIN
	DECLARE totalStar double default 0;
    DECLARE totalRating int default 0;
    DECLARE newAverageRating double default 0;

    SELECT COUNT(*), SUM(star) INTO totalRating, totalStar FROM rating WHERE rating.bookID = NEW.bookID;
    
    IF totalRating > 0 THEN
		SET newAverageRating := totalStar / totalRating;
    END IF;
    UPDATE book SET avgRating = newAverageRating WHERE book.id=new.bookID;
END//
DELIMITER ;

drop trigger if exists avgRatingAfterDeleteTrigger;
DELIMITER //
CREATE TRIGGER avgRatingAfterDeleteTrigger
AFTER delete ON rating
FOR EACH ROW
BEGIN
	DECLARE totalStar double default 0;
    DECLARE totalRating int default 0;
    DECLARE newAverageRating double default 0;

    SELECT COUNT(*), SUM(star) INTO totalRating, totalStar FROM rating WHERE rating.bookID = old.bookID;
    
    IF totalRating > 0 THEN
		SET newAverageRating := totalStar / totalRating;
    END IF;
    UPDATE book SET avgRating = newAverageRating WHERE book.id=old.bookID;
END//
DELIMITER ;
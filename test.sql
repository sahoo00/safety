/* search Circle
SELECT LastName, FirstName, username, Email,
    SafetyCircle.relationship from Persons 
        left join SafetyCircle
            on SafetyCircle.PID1 = 1 AND Persons.ID=SafetyCircle.PID2
            WHERE FirstName LIKE '%b%' LIMIT 100;
SELECT LastName, FirstName, username, Email,
    SafetyCircle.relationship from Persons LEFT JOIN SafetyCircle
    on SafetyCircle.PID1 = 1 AND Persons.ID=SafetyCircle.PID2
    where LastName LIKE '%$search%' OR FirstName LIKE '%$search%' OR
    username LIKE '%$search%' OR Email LIKE '%$search%' LIMIT 100;
*/

/*
SELECT LastName, FirstName, username, Email,
  t1.TID, EXISTS(select 1 from Responses where TID=t1.TID) as
  Response
  from Persons inner join
    (select Triggers.TID, Triggers.PID, Triggers.event from 
    Triggers WHERE Triggers.PID=1 AND Triggers.status = 'open') t1
              on Persons.ID = t1.PID
*/


/*
SELECT TID,event from Triggers inner join Persons
    on Triggers.PID = Persons.ID AND Persons.username = 'sahoo00'
        order by Triggers.event DESC
*/

/*
CREATE FUNCTION slc (lat1 double, lon1 double, lat2 double, lon2 double)
  RETURNS double
  RETURN 6371 * acos(cos(radians(lat1)) * cos(radians(lat2)) *
    cos(radians(lon2) - radians(lon1)) + sin(radians(lat1)) *
    sin(radians(lat2)));
*/

/*
SELECT LastName, FirstName, username, Email,
    slc( (select lat from Location where PID = 4),
  (select lon from Location where PID = 4), lat, lon) as distance,
  t2.TID, EXISTS(select 1 from Responses where TID=t2.TID AND PID=4) as
  Response
  from Persons inner join
  (select t1.TID, t1.PID, lat, lon from
    (select Triggers.TID, Triggers.PID, Triggers.event from 
    Triggers inner join SafetyCircle
    on SafetyCircle.PID1 = 4 AND Triggers.PID=SafetyCircle.PID2) t1
            inner join Location on Location.PID = t1.PID) t2
              on Persons.ID = t2.PID
*/

/*

SELECT LastName, FirstName, username, Email,
slc( (select lat from Location where PID = 1), 
  (select lon from Location where PID = 1), lat, lon) as distance
from Persons inner join 
(select t1.PID, lat, lon from 
  (select Responses.PID from Responses inner join Triggers
    on Responses.TID = Triggers.TID AND Triggers.PID = 1
    AND Triggers.status='open') t1 inner join Location
  on Location.PID = t1.PID) t2
on Persons.ID = t2.PID
*/

/*
use shanvish_safety
CREATE TABLE test 
(
id INTEGER,
userName text,
grade INTEGER,
PRIMARY KEY(id)
);
CREATE TABLE test  ( id INTEGER, userName text, grade INTEGER, PRIMARY KEY(id) );
INSERT into test VALUES (1, 'Debashis Sahoo', 0),
    (2, 'Sonlisa Pandey', 10);
INSERT into test VALUES (1, 'Debashis Sahoo', 0),     (2, 'Sonlisa Pandey', 10);
select * from test
;
select * from test;
drop table test;
connect
drop table test;
CREATE TABLE test 
(
id INTEGER,
userName text,
grade INTEGER,
PRIMARY KEY(id)
);
CREATE TABLE test  ( id INTEGER, userName text, grade INTEGER, PRIMARY KEY(id) );
INSERT into test VALUES (1, 'Debashis Sahoo', 0),
    (2, 'Sonlisa Pandey', 10);
INSERT into test VALUES (1, 'Debashis Sahoo', 0),     (2, 'Sonlisa Pandey', 10);
select * from test;
connect
select * from test;
connect
select * from test;
help
select * from test;
connect
select * from test;
h
? */

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

use shanvish_safety;
DROP TABLE IF EXISTS Prices;
CREATE TABLE Prices
(
Dtype varchar(20),
Version varchar(20),
Price FLOAT,
PRIMARY KEY(Dtype)
);
insert into Prices VALUES('TRG1', 'S1 1.0', 4.3);
insert into Prices VALUES('TRG2', 'S2 1.0', 20.3);
insert into Prices VALUES('MN', 'S3 1.0', 2.5);
DROP TABLE IF EXISTS Devices;
CREATE TABLE Devices
(
ID BIGINT(20) NOT NULL AUTO_INCREMENT,
Dtype varchar(20),
CKey varchar(20),
PID BIGINT(20),
PRIMARY KEY(ID)
);
DROP TABLE IF EXISTS Persons;
CREATE TABLE Persons
(
ID BIGINT(20) NOT NULL AUTO_INCREMENT,
LastName varchar(50) NOT NULL,
FirstName varchar(50),
Email varchar(255),
Address varchar(255),
City varchar(50),
Country varchar(50),
State varchar(50),
zipcode varchar(10),
username varchar(70),
password varchar(70),
token varchar(128),
role varchar(20), active varchar(50), last varchar(50),
UNIQUE (username),
PRIMARY KEY (ID)
);
DROP TABLE IF EXISTS Owner;
CREATE TABLE Owner
(
DID BIGINT(20) NOT NULL,
PID BIGINT(20),
PRIMARY KEY (DID)
);
DROP TABLE IF EXISTS Location;
CREATE TABLE Location
(
DID BIGINT(20),
PID BIGINT(20),
lat DOUBLE,
lon DOUBLE,
PRIMARY KEY (DID, PID)
);
DROP TABLE IF EXISTS Triggers;
CREATE TABLE Triggers
(
TID BIGINT(20) NOT NULL AUTO_INCREMENT,
DID BIGINT(20),
PID BIGINT(20),
event DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
status varchar(10),
endTime DATETIME,
PRIMARY KEY (TID)
);
DROP TABLE IF EXISTS Responses;
CREATE TABLE Responses
(
TID BIGINT(20) NOT NULL,
PID BIGINT(20) NOT NULL,
event DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
status varchar(10),
delivery DATETIME,
PRIMARY KEY (TID, PID)
);
DROP TABLE IF EXISTS SafetyCircle;
CREATE TABLE SafetyCircle
(
PID1 BIGINT(20) NOT NULL,
PID2 BIGINT(20) NOT NULL,
relationship varchar(20),
PRIMARY KEY (PID1, PID2)
);

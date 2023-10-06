DECLARE @type_primary INT = 1;
DECLARE @type_people INT = 2;
DECLARE @type_city INT = 3;
DECLARE @type_material INT = 4;

DROP TABLE dbo.material;
CREATE TABLE dbo.material
(
    id    INT IDENTITY PRIMARY KEY,
    type  INT CHECK (type BETWEEN 1 AND 4),
    name  VARCHAR(50),
    icon  VARCHAR(50),
    color INT,
    hexColor AS CONVERT(VARBINARY(8), color),

    UNIQUE (type, name),
    UNIQUE (icon, color),
);



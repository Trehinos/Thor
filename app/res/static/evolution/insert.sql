DECLARE @type_primary INT = 1;
DECLARE @type_people INT = 2;
DECLARE @type_city INT = 3;
DECLARE @type_material INT = 4;

INSERT INTO material (type, name, icon, color)
VALUES
    (@type_primary, 'faith', 'praying-hands', 0x999999ff),
    (@type_primary, 'wellness', 'coins', 0xddaa44ff),
    (@type_primary, 'happiness', 'face-smile', 0xdd7755ff),
    (@type_primary, 'culture', 'palette', 0x9966aaff),
    (@type_primary, 'technic', 'brain', 0x6699eeff);

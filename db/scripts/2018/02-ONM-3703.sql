INSERT INTO user_groups(name, type, enabled)
SELECT 'Newsletter', 1, 1 FROM user_groups WHERE NOT EXISTS(
    SELECT * FROM user_groups WHERE name ='Newsletter'
)  LIMIT 1;

INSERT INTO user_groups_privileges
SELECT pk_user_group, 224 FROM user_groups WHERE `name` = 'Newsletter' LIMIT 1;

SELECT @group := `pk_user_group`
FROM user_groups WHERE pk_user_group IN (
    SELECT pk_fk_user_group
    FROM user_groups_privileges
    WHERE pk_fk_privilege = 224
) LIMIT 1;

INSERT INTO users(username, email, name, type, fk_user_group, activated)
SELECT email, email, CONCAT(`pc_users`.`firstname`, " ", `pc_users`.`lastname`), 1, 'pc_user', subscription
FROM pc_users WHERE NOT EXISTS (SELECT id FROM `users` WHERE `users`.email = `pc_users`.email);

INSERT INTO user_user_group(user_id, user_group_id, status)
SELECT id, @group, activated FROM users WHERE fk_user_group='pc_user' AND email IN (SELECT email FROM `pc_users`);

UPDATE users SET fk_user_group=@group WHERE fk_user_group='pc_user';

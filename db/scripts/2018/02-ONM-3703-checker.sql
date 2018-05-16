SELECT count(id) FROM `users` WHERE id IN (
    SELECT user_id FROM user_user_group WHERE user_group_id = (select pk_user_group from user_groups WHERE name="Newsletter" LIMIT 1)
);

SELECT count(pk_pc_user) FROM pc_users;

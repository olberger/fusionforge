DROP TABLE project_perm ;
DROP VIEW project_perm ;

CREATE VIEW project_perm AS
       SELECT
	0 AS id,
	role_setting.ref_id AS group_project_id,
	user_group.user_id,
	role_setting.value::int AS perm_level
       FROM role_setting, user_group
       WHERE user_group.role_id = role_setting.role_id
         AND role_setting.section_name='pm';

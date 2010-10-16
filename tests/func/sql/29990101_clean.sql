UPDATE users SET email='admin@debug.log' WHERE user_name='admin';

DELETE FROM group_plugin;
DELETE FROM plugins WHERE plugin_name = 'scmcvs';
DELETE FROM plugins WHERE plugin_name = 'scmsvn';
DELETE FROM plugins WHERE plugin_name = 'cvstracker';

INSERT INTO plugins(plugin_name,plugin_desc) values ('websvn','WebSVN plugin');

DELETE FROM user_session;
DELETE FROM activity_log;
DELETE FROM form_keys;
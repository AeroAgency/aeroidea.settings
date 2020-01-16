<?php return array (
    'title' => '', //  название конфига
    'migration_dir' => '', // директория для миграций по умолчанию: /local/php_interface/migrations или /bitrix/php_interface/migrations
    'migration_table' => '', //  таблица в бд с миграциями, по умолчанию sprint_migration_versions
    'migration_extend_class' => '', // класс, от которого наследуются миграции, по умолчанию Version
    'version_prefix' => '', // Заголовок класса миграции, по умолчанию Version
    'stop_on_errors' => false, // Останавливать выполнение миграций при появлении ошибок, варианты значений: true | false, по умолчанию false (не останавливать)
    'show_admin_interface' => true, // Показывать сервис миграций в админке, варианты значений: true | false, по умолчанию true (показывать)
    'console_user' => 'admin', // Пользователь, от которого запускаются миграции в консоли, варианты значений: admin | login:userlogin | false, по умолчанию admin (запускать от админа)
    'version_builders' => ['ConfigurationMigrationBuilder' => '\Aeroidea\Settings\Migrations\Builders\ConfigurationMigrationBuilder'], // Конструкторы
    'version_filter' => [], // Массив, по которому будет фильтроваться и выполняться список миграций
    // version_name_template, -  Шаблон названия файла миграции, должен содержать строки #NAME# и #TIMESTAMP#
);
<?php
/*
 * An API to insert all the locales object found in built-in PHP into the
 * crud.db DB.
 * 
 * Example usage:
 *   crud-create-locales.php?drop_table=true
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

function create_data() {
    $pdo = new PDO('sqlite:crud.db');

    $drop_table = boolval($_GET['drop_table'] ?? false);
    if ($drop_table) {
        $data["drop_table"] = $pdo->exec("DROP TABLE locales");
        if (false === $data['drop_table']) {
            return ["error" => "Failed to drop table locales"];
        }
    }

    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='locales'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (false === $result) {
        $result = $pdo->exec(<<<HERE
            CREATE TABLE locales (
                code TEXT primary key,
                language TEXT,
                region TEXT,
                script TEXT
            );
            HERE
        );
        if (false === $result) {
            return ["error" => "Failed to create table."];
        }

        $stmt = $pdo->prepare('INSERT INTO locales(code, language, region, script) VALUES(?, ?, ?, ?)');
        $locale_codes = ResourceBundle::getLocales('');
        foreach ($locale_codes as $code) {
            $locales = array(
                "code" => $code,
                "language" => Locale::getDisplayLanguage($code),
                "region" => Locale::getDisplayRegion($code),
                "script" => Locale::getDisplayScript($code)
            );
            $result = $stmt->execute(array_values($locales));
            if (false === $result) {
                return ["error" => "Failed to insert data row.", "data_row" => $locales];
            }
        }

        $data = ['success' => "Data loaded into table locales."];
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM locales");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ['error' => "Table 'locales' already exists!", "count" => $result['count']];
    }

    $data['timestamp'] = date('c');
    return $data;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode(create_data());

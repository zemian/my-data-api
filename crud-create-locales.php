<?php
/*
 * A script to insert all the locales object found in built-in PHP into the
 * crud.db DB.
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

function create_data() {
    $pdo = new PDO('sqlite:crud.db');

    $is_reset = isset($_GET['reset']);
    if ($is_reset) {
        $pdo->exec("DROP TABLE locales");
    }

    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='locales'");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data === false) {
        $result = $pdo->exec(<<<HERE
            CREATE TABLE locales (
                code TEXT primary key,
                language TEXT,
                region TEXT,
                script TEXT
            );
            HERE
        );

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
        }

        $data = ['message' => "Table 'locales' created!",
            'timestamp' => date('c'),
            'reset' => $is_reset
        ];
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM locales");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = ['message' => "Table 'locales' already exists!",
            'timestamp' => date('c'),
            'count' => $result['count']
        ];
    }
    return $data;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode(create_data());

<?php
/*
 * A API to load static data files into crud.db DB.
 * 
 * The input file must contain a list of object data in JSON format. Each object key is used as
 * the DB field name.
 * 
 * Example usage:
 *   crud-create-data.php?file=states.json&table=states&columns=code PRIMARY KEY,name TEXT&drop_table=true&create_table=true
 *   http://localhost/apps/my-data-api/crud-create-data.php?file=countries.json&table=countries&columns=code%20PRIMARY%20KEY,name%20TEXT&drop_table=true&create_table=true
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

function create_data() {
    $data = [];
    $pdo = new PDO('sqlite:crud.db');

    $file = $_GET['file'] ?? '';
    if (!$file) {
        return ["error" => "Missing data file parameter."];
    }

    $table = $_GET['table'] ?? '';
    $columns = $_GET['columns'] ?? '';
    if (!$table || !$columns) {
        return ["error" => "Missing table and columns parameters."];
    }

    $drop_table = boolval($_GET['drop_table'] ?? false);
    if ($drop_table) {
        $data["drop_table"] = $pdo->exec("DROP TABLE $table");
        if (false === $data['drop_table']) {
            return ["error" => "Failed to drop table $table"];
        }
    }

    $create_table = boolval($_GET['create_table'] ?? false);
    if ($drop_table || $create_table) {
        $sql = "CREATE TABLE $table ( $columns )";
        try {
            $data['create_table'] = $pdo->exec($sql);
        } catch (Exception $e) {
            return ["error" => "Failed to create table $table", "exception" => $e];
        }
    } else {
        $stmt = $pdo->query("SELECT count(*) as count FROM $table");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (false !== $result) {
            return ["error" => "Table already exists.", "count" => $result["count"]];
        }
    }

    $data_dir = "./data";
    $data_file = realpath("$data_dir/$file");
    if (!is_file($data_file)) {
        return ["error" => "File not found."];
    } else {
        $data_list = json_decode(file_get_contents($data_file), true);
        $column_fields = [];
        $column_qmarks = [];
        foreach (explode(",", $columns) as $col) {
            $col = trim(explode(" ", $col)[0]);
            $column_fields[] = $col;
            $column_qmarks[] = "?";   
        }
        $sql = "INSERT INTO $table (" . implode(", ", $column_fields) . 
            ") VALUES(" . implode(", ", $column_qmarks) . ")";
        var_dump($sql);
        $stmt = $pdo->prepare($sql);
        foreach ($data_list as $data) {
            $result = $stmt->execute(array_values($data));
            if (false === $result) {
                return ["error" => "Failed to insert data row.", "data_row" => $data];
            }
        }

        $data = ["success" => "Data loaded into table $table."];
    }

    $data['timestamp'] = date('c');
    return $data;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode(create_data());

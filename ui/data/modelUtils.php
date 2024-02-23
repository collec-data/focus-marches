<?php
function appendCondition($sql, $condition) {
    if (stripos($sql, 'WHERE') !== false) {
        // If there is already a WHERE clause, append with AND
        $sql .= " AND $condition";
    } else {
        // If there is no WHERE clause, add one
        $sql .= " WHERE $condition";
    }
    return $sql;
}

function prepareAndExecute($db, $sql, $params, $types) {
    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    $stmt->execute();

    return $stmt;
}

?>
<?php

require __DIR__ . '/mail-helper.php';

$props = array();
parse_str($_SERVER['QUERY_STRING'], $props);

function tableRow($key, $value, $encode_uri = true) {
    return "<tr><td style='vertical-align: top; border: none;'>" . str_replace(["\n", "\r"], "</br>", ($encode_uri ? htmlentities($key) : $key)) . ":</td><td style='vertical-align: top; border: none;'>" . str_replace(["\n", "\r"], "</br>", ($encode_uri ? htmlentities($value) : $value)) . "</td></tr>";
}

function table(...$rows) {
    $tableStr = "<table style='border: none;'>";
    foreach ($rows as &$row) {
        if (($row["key"] ?? $row[0]) && ($row["value"] ?? $row[1])) {
            $tableStr .= tableRow($row["key"] ?? $row[0], str_replace(["\n", "\r"], "</br>", $row["value"] ?? $row[1]), $row["encode_uri"] ?? $row[2] ?? true);
        }
    }
    $tableStr .= "</table>";
    return $tableStr;
}

$email_msg = "<b>A new sublet offer has been posted:</b>";

$email_msg .= table(
    ["Name of Individual Offering Sublet", $props["name"]],
    ["NYU Department Affiliation", $props["department"]],
    ["Contact Phone", ($props["phone"] ? "<a href='tel:" . htmlentities($props["phone"]) . "' target='_blank'>" . htmlentities($props["phone"]) . "</a>" : null), false],
    ["Contact Email", ($props["email"] ? "<a href='mailto:" . htmlentities($props["email"]) . "' target='_blank'>" . htmlentities($props["email"]) . "</a>" : null), false],
    ["Sublet Start Date (mm/dd/yyyy)", $props["start_date"]],
    ["Sublet End Date (mm/dd/yyyy)", $props["end_date"]],
    ["Address", $props["address"]],
    ["Number of Bedrooms", $props["num_of_bedroom"]],
    ["Number of Bathrooms", $props["num_of_bathroom"]],
    ["Monthly Rent", $props["rent"]],
    ["Monthly Utilities ", $props["utilities"]],
    ["Monthly Cable/Internet/Phone", $props["network"]],
    ["Smoking Allowed", $props["smoking"]],
    ["Pets Allowed", $props["pets"]],
    ["Water Plants", $props["water_plants"]],
    ["Storage Restrictions", $props["storage_restrictions"]]
);

send_email(
    $props["name"],
    $props["email"],
    (($props["dev"] ?? null) ? (($props["dev_email"] ?? null) ? $props["dev_email"] : "nyu-faculty-housing-group@outlook.com") : "faculty-housing-test-external-group@googlegroups.com"),
    "New Sublet Offer",
    $email_msg
);

?>

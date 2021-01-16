<?php

require __DIR__ . '/mail-helper.php';

$props = array();
parse_str($_SERVER['QUERY_STRING'], $props);

function tableRow($key, $value) {
    return "<tr><td style='padding: 5px 15px 5px 0px; vertical-align: top;'><b>" . htmlentities($key) . ":</b></td><td style='padding: 5px 0; vertical-align: top;'>" . htmlentities($value) . "</td></tr>";
}

function table(...$rows) {
    $tableStr = "<table>";
    foreach ($rows as &$row) {
        if (($row["key"] ?? $row[0]) && ($row["value"] ?? $row[1])) {
            $tableStr .= tableRow($row["key"] ?? $row[0], str_replace(["\n", "\r"], "</br>", $row["value"] ?? $row[1]));
        }
    }
    $tableStr .= "</table>";
    return $tableStr;
}

$email_msg = "";

$email_msg .= "<h2>" . "Contact Information" . "</h2>";
$email_msg .= table(
    ["Full Name", $props["name"]],
    ["Department", $props["department"]],
    ["Phone", $props["phone"]],
    ["Email", $props["email"]]
);
$email_msg .= "</br>";

$email_msg .= "<h2>" . "Sublet Term" . "</h2>";
$email_msg .= table(
    ["Start Date (mm/dd/yyyy)", $props["start_date"]],
    ["End Date (mm/dd/yyyy)", $props["end_date"]]
);
$email_msg .= "</br>";

$email_msg .= "<h2>" . "Unit Information" . "</h2>";
$email_msg .= table(
    ["Address", $props["address"]],
    ["Number of Bedrooms", $props["num_of_bedroom"]],
    ["Number of Bathrooms", $props["num_of_bathroom"]],
    ["Current Monthly Rent", $props["rent"]],
    ["Current Monthly Utilities Charges", $props["utilities"]],
    ["Current Monthly Cable/Internet/Phone Charges", $props["network"]],
    ["No Smoking", $props["no_smoking"]],
    ["No Pets", $props["no_pets"]],
    ["Water Plants", $props["water_plants"]],
    ["Some closets will be reserved for storage", $props["reserved_closets"]]
);

send_email(
    $props["name"],
    $props["email"],
    (($props["dev"] ?? null) ? (($props["dev_email"] ?? null) ? $props["dev_email"] : "nyu-faculty-housing-group@outlook.com") : "faculty-housing-test-external-group@googlegroups.com"),
    "New Sublet Offer",
    $email_msg
);

?>

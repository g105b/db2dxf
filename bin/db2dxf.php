#!/usr/bin/php
<?php
/**
 *
 */

namespace db2dxf;

$autoloader = require(__DIR__ . "/../vendor/autoload.php");
$autoloader->addPsr4("DXF\\", realpath(
	__DIR__ . "/../vendor/g105b/dxfwriter/src/DXF/"));

use db2dxf\Element\Element;
use \PDO;
use \DXF\Writer;
use db2dxf\Element\ElementFactory;

echo "\n";

$config = [
	"database" => "dxfdata",
	"drawing" => "",
	"file" => "output.dxf",
	"hostname" => "localhost",
	"port" => "3306",
	"username" => "root",
	"password" => "",
];

if(!isset($argv)) {
	$argv = [];
}

if(count($argv) < (count($config)) ) {
	echo "Invalid arguments. Usage:\n\tdb2dxf ";
	foreach($config as $c => $value) {
		echo "[$c] ";
	}
	echo "\n\n";
	exit(1);
}

$config["database"] = $argv[1];
$config["drawing"] = $argv[2];
$config["file"] = $argv[3];
$config["hostname"] = $argv[4];
$config["port"] = $argv[5];
$config["username"] = $argv[6];
$config["password"] = isset($argv[7]) ? $argv[7] : "";

$db = null;
$dsn = "mysql:dbname=$config[database];host=$config[hostname]";

try {
	$db = new PDO($dsn, $config["username"], $config["password"], [
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);
}
catch(PDOException $ex) {
	echo "Connection failed: " . $ex->getMessage() . "\n\n";
	exit(1);
}

$stmt = $db->prepare(file_get_contents(
	__DIR__ . "/../sql/get-drawing-by-name.sql"));

$stmt->execute([":drawing" => $config["drawing"]]);
$drawing = $stmt->fetch();

if(!$drawing) {
	echo "No drawing found with name $config[drawing].\n\n";
	exit(1);
}

echo "Found drawing with ID $drawing[ID].\n\n";

$stmt = $db->prepare(file_get_contents(
	__DIR__ . "/../sql/get-all-elements.sql"));

$stmt->execute([":ID_Drawing" => $drawing["ID"]]);

echo "Building DXF...\n";

$writer = new Writer();

// Root keys = element ID.
// Child keys = parent ID.
$elementArray = [
	"root" => [],
	"child" => [],
];

// Loop over the elements and store them in memory:
while(false !== ($row = $stmt->fetch())) {
	if(is_null($row["parent"])) {
		$elementArray["root"][(int)$row["ID"]]
			= ElementFactory::create($row["ID"], $row["type"]);
	}
	else {
		$elementArray["child"][(int)$row["ID"]]
			= ElementFactory::create($row["ID"], $row["type"], $row["parent"]);
	}
}

// Assign attributes to in-memory elements:
foreach($elementArray as $elementType => $elArray) {
	foreach($elArray as $i => $el) {
		echo "Assigning attributes to element $el->ID ($el->type).\n";
		$el->assignAttributes(getAttributes($el, $db));
	}
}

/**
 * Returns an array of Attributes associated to the element in the database.
 */
function getAttributes(Element $element, PDO $db) {
	$attributeArray = [];
	$multiAttributeArray = [];

	// Assign single attribute KVPs:
	$stmt = $db->prepare(file_get_contents(
		__DIR__ . "/../sql/get-single-attributes.sql"));

	$stmt->execute([":ID_Element" => $element->ID]);
	while(false !== ($row = $stmt->fetch())) {
		$value = $row["value"];
		if(substr($value, 0, 1) === "[") {
			$value = explode(",", substr($value, 1, -1));
		}

			echo "\tAttribute $row[key]...\n";
		$attributeArray[$row["key"]] = $value;
	}

	// Assign multi-attribute KVPs:
	$stmt = $db->prepare(file_get_contents(
		__DIR__ . "/../sql/get-multi-attributes.sql"));
	$stmt->execute([":ID_Element" => $element->ID]);

	while(false !== ($row = $stmt->fetch()) ) {
		if(!isset($multiAttributeArray[$row["key"]])) {
			$multiAttributeArray[$row["key"]] = [];
		}

		$value = $row["value"];
		if(substr($value, 0, 1) === "[") {
			$value = explode(",", substr($value, 1, -1));
		}

		echo "\tMulti-Attribute $row[key]...\n";
		$multiAttributeArray []= $value;
	}

	return $attributeArray;
}

<?php
namespace db2dxf\Element;

abstract class Element {

public $ID;
public $type;
public $parentID;
private $attributeArray;

public function __construct($ID, $type, $parentID = null) {
	$this->ID = (int)$ID;
	$this->type = $type;
	$this->parentID = $parentID;
}

public function assignAttributes($attributeArray) {
	$this->attributeArray = $attributeArray;
}

}#

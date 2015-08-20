<?php
namespace db2dxf\Element;

abstract class Element {

public $ID;
public $type;
public $parentID;
private $attributeArray = [];

public function __construct($ID, $type, $parentID = null) {
	$this->ID = (int)$ID;
	$this->type = $type;
	if(!is_null($parentID)) {
		$this->parentID = (int)$parentID;
	}
}

public function assignAttributes($attributeArray) {
	$this->attributeArray = $attributeArray;
}

public function getParams() {
	return $this->attributeArray;
}

}#

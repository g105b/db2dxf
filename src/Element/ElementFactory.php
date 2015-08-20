<?php
namespace db2dxf\Element;

class ElementFactory {

public static function create($ID, $type, $parentID = null) {
	return new AbstractElement($ID, $type, $parentID);
}

}#

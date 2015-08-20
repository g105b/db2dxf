<?php
namespace db2dxf\Element;

class ElementFactory {

public static function create($ID, $type) {
	return new AbstractElement($ID, $type);
}

}#

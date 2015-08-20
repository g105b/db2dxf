<?php
namespace db2dxf\Writer;

use DXF\Writer as W;
use DXF\Arc;
use DXF\Block;
use DXF\Circle;
use DXF\Collection;
use DXF\Entity;
use DXF\Face;
use DXF\Insert;
use DXF\Line;
use DXF\LineType;
use DXF\LwPolyLine;
use DXF\Point;
use DXF\PolyLine;
use DXF\Solid;
use DXF\Style;
use DXF\Text;
use DXF\View;
use ReflectionClass;

class Writer {

private $elementArray;

public function __construct($elementArray) {
	$this->elementArray = $elementArray;
}

public function write($filename) {
	$w = new W();

	$baseObjects = [];

	foreach($this->elementArray as $type => $elements) {
		$c = count($elements);
		$i = 0;
		foreach($elements as $id => $el) {
			$i++;
			echo "\tWriting $i of $c $type elements...\n";

			$className = ucfirst($el->type);
			$reflector = new ReflectionClass("\\DXF\\$className");
			$class = $reflector->newInstanceArgs([$el->getParams()]);

			if($type === "root") {
				$w->append($class);
				$baseObjects[$el->ID] = $class;
			}
			else {
				$baseObjects[$el->parentID]->append($class);
			}
		}
	}

	$w->appendStyle(new Style());
	$w->appendView(new View(array('name' =>'Normal')));
	$w->appendView(View::byWindow('Window', array(1,0), array(2,1)));

	$w->saveAs($filename);
}

}#

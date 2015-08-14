# db2dxf

Database to DXF script.

Database object definitions
---------------------------

### `Drawing`

A drawing is the whole collection of elements within the DXF file.

* `ID` INT
* `name` VARCHAR(32)

### `Element`

An element is an individual abstract component of the drawing. An element on its own has no intrinsic position or size, as this data is stored against an element using an `Attribute` or `Attribute_List`.

All elements must be associated to a `Drawing`, must have an `Element_Type`, and can be children of other Elements using the parent field.

* `ID` INT
* `ID_Drawing` INT FK[Drawing:ID]
* `ID_Element_Type` INT FK[Element_Type:ID]
* `ID_Element__parent INT FK[Element:ID] (Optional)
* `name` VARCHAR(32)
* `description` VARCHAR(128)

### `Element_Type`

An Element Type acts as an enumeration of the different types available to the DXF format, such as `Circle`, `Spline`, `Face`, etc.

* `ID` INT
* `name` VARCHAR(32)

### `Attribute`

Defines a single attribute associated to an Element with a single value. An Attribute is a key-value-pair.

* `ID` INT
* `ID_Element INT FK[Element:ID]
* `key` VARCHAR(32)
* `value` VARCHAR(128)

### `Attribute_List`

Defines a single attribute associated to an Element with multiple values. An Attribute List is a key with multiple values associated. Each value is stored as an `Attribute_List_Item` record.

* `ID` INT
* `ID_Element INT FK[Element:ID]
* `key` VARCHAR(32)

### `Attribute_List_Item`

Stores a list of values associated to an Attribute List.

* `ID` INT
* `ID_Attribute_List INT FK[Attribute_List:ID]
* `value` VARCHAR(128)

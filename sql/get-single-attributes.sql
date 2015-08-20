select
  Attribute.key,
  Attribute.value

from
  Attribute

where
  ID_Element = :ID_Element

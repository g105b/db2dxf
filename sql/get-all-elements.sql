select
  Element.ID,
  Element.ID_Element__parent as parent,
  Element.name,
  Element.description,
  Element_Type.name as type

from
  Element

  inner join Element_Type
    on Element.ID_Element_Type = Element_Type.name

where
  Element.ID_Drawing = :ID_Drawing

order by
  ID_Element__parent

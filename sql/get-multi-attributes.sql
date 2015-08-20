select
  Attribute_List.key,
  Attribute_List_Item.value

from
  Attribute_List

join Attribute_List_Item
  on Attribute_List_Item.ID_Attribute_List = Attribute_List.ID

where
  ID_Element = :ID_Element

order by
  Attribute_List.key,
  Attribute_List_Item.ID

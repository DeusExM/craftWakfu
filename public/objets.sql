INSERT INTO inventory_items ( item_id, 1, 51)
SELECT i.item_id FROM item i where i.name = 'Bec de Piou';

INSERT INTO inventory_items ( item_id, 1, 4)
SELECT i.item_id FROM item i where i.name = 'Nacre Exotique';

INSERT INTO inventory_items ( item_id, 1, 2)
SELECT i.item_id FROM item i where i.name = 'Cocon de larve';

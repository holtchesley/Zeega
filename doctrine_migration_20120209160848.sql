# Doctrine Migration File Generated on 2012-02-09 16:02:48

# Version 20120209145310
ALTER TABLE Node RENAME Frame;
ALTER TABLE Route RENAME Sequence;
ALTER TABLE Playground RENAME Site;
ALTER TABLE Users_Playgrounds RENAME users_sites;
ALTER TABLE routes_layers RENAME sequences_layers;
ALTER TABLE Item DROP FOREIGN KEY FK_BF298A20F8FE9381;
DROP INDEX IDX_BF298A20F8FE9381 ON Item;
ALTER TABLE Item CHANGE playground_id sequence_id INT DEFAULT NULL;
ALTER TABLE Project DROP FOREIGN KEY FK_E00EE972F8FE9381;
ALTER TABLE Project CHANGE playground_id site_id INT DEFAULT NULL;
ALTER TABLE Item ADD CONSTRAINT FK_BF298A20F6BD1646 FOREIGN KEY (site_id) REFERENCES Site(id);
CREATE INDEX IDX_BF298A20F6BD1646 ON Item (site_id);
DROP INDEX IDX_E00EE972F8FE9381 ON Project;
ALTER TABLE Project ADD CONSTRAINT FK_E00EE972F6BD1646 FOREIGN KEY (site_id) REFERENCES Site(id);
CREATE INDEX IDX_E00EE972F6BD1646 ON Project (site_id);

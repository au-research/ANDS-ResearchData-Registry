USE dbs_roles;

-- superuser/superuser
INSERT INTO `authentication_built_in` VALUES (1,'superuser','8e67bb26b358e2ed20fe552ed6fb832f397a507d','SYSTEM','2014-08-07 02:37:17','2014-08-07 02:37:17','SYSTEM');

-- useruser is a REGISTRY_SUPERUSER
INSERT INTO `role_relations` VALUES (1,'REGISTRY_SUPERUSER','superuser','SYSTEM','2014-08-07 02:37:17','2014-08-07 02:37:17','SYSTEM'),(2,'TEST_ORGANISATION','superuser','SYSTEM','2014-08-07 02:37:17','2014-08-07 02:37:17','SYSTEM');

-- functional roles and 1 organisational role
INSERT INTO `roles` VALUES
    (1,'REGISTRY_SUPERUSER','ROLE_FUNCTIONAL','Superuser','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (2,'PUBLIC','ROLE_FUNCTIONAL','Public','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (3,'COSI_BUILT_IN_USERS','ROLE_FUNCTIONAL','COSI Built-in Authentication User','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (4,'REGISTRY_STAFF','ROLE_FUNCTIONAL','Registry Staff Member','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (5,'REGISTRY_USER','ROLE_FUNCTIONAL','Registry Data Source Admin','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (6,'PIDS_USER','ROLE_FUNCTIONAL','PIDS Users','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (7,'SPOTLIGHT_CMS_EDITOR','ROLE_FUNCTIONAL','Spotlight CMS Editor','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (8,'PORTAL_STAFF','ROLE_FUNCTIONAL','Portal/CMS Staff Member','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (9,'DOI_USER','ROLE_FUNCTIONAL','DOI Service User','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (10,'DOIS_USER','ROLE_FUNCTIONAL','DOI User','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (11,'VOCAB_USER','ROLE_FUNCTIONAL','Vocabulary Catalogue User','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (12,'SHIB_AUTHENTICATED','ROLE_FUNCTIONAL','Shibboleth Authenticated Users','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (13,'TEST_ORGANISATION','ROLE_ORGANISATIONAL','Test Organisation Role','','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-04 00:31:38','2014-09-04 00:31:38',NULL,NULL,NULL, NULL, NULL),
    (14,'superuser','ROLE_USER','Admin User','AUTHENTICATION_BUILT_IN','1','2014-08-07 02:37:17','SYSTEM','SYSTEM','2014-09-24 23:00:25','2014-09-25 09:00:25',NULL,NULL,NULL, NULL, NULL);
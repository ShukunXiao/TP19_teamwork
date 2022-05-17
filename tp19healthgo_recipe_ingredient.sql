-- MySQL dump 10.13  Distrib 8.0.29, for macos12 (x86_64)
--
-- Host: tp19server.mysql.database.azure.com    Database: tp19healthgo
-- ------------------------------------------------------
-- Server version	5.7.37-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `recipe_ingredient`
--

DROP TABLE IF EXISTS `recipe_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_ingredient` (
  `recipe_ingredient_id` varchar(45) NOT NULL,
  `recipe_ingredient` varchar(45) NOT NULL,
  PRIMARY KEY (`recipe_ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_ingredient`
--

LOCK TABLES `recipe_ingredient` WRITE;
/*!40000 ALTER TABLE `recipe_ingredient` DISABLE KEYS */;
INSERT INTO `recipe_ingredient` VALUES ('I1','winter squash'),('I10','milk'),('I100','ground meat substitute'),('I101','black peppercorns'),('I102','wheat flour'),('I103','ground chuck'),('I104','dried pinto beans'),('I105','tomatillos'),('I106','bow tie pasta'),('I107','mozzarella string cheese'),('I108','bell pepper'),('I109','baby spinach leaves'),('I11','frozen chopped spinach'),('I110','cheese'),('I111','spinach'),('I112','garlic cloves'),('I113','strawberry cake mix'),('I114','fat-free refried beans'),('I115','all-bran cereal'),('I116','asparagus'),('I117','mashed potatoes'),('I118','plain breadcrumbs'),('I119','black beans'),('I12','tri-color spiral pasta'),('I120','chili without beans'),('I121','mexican rice'),('I122','frozen hash brown potatoes'),('I123','cooking spray'),('I124','dry lentils'),('I125','arborio rice'),('I126','10 bean soup mix'),('I127','cream of spinach soup mix'),('I128','bread flour'),('I129','textured vegetable protein'),('I13','frozen french fries'),('I130','warm water'),('I131','whole wheat flour'),('I132','egg whites'),('I133','honey'),('I134','hot water'),('I135','yeast'),('I136','soup mix'),('I137','15 bean soup mix'),('I138','chicken breast tenders'),('I139','fresh linguine'),('I14','flour'),('I140','boneless skinless chicken breast halves'),('I141','tiger shrimp'),('I142','dijon mustard'),('I143','basmati rice'),('I144','large shrimp'),('I145','beans'),('I146','nonstick cooking spray'),('I147','bacon grease'),('I148','bananas'),('I149','scallops'),('I15','fresh corn'),('I150','splenda granular'),('I151','sun-dried tomato'),('I152','cooked pasta'),('I153','maggi 2-minute noodles'),('I154','sour apple schnapps'),('I155','small potatoes'),('I156','chicken breast'),('I157','chicken stock'),('I158','low sodium chicken broth'),('I159','bulb of garlic'),('I16','unflavored gelatin'),('I160','crusty italian bread'),('I161','gingerbread cake mix'),('I162','sweet potato'),('I163','ground turkey breast'),('I164','fresh tomatoes'),('I165','potatoes'),('I166','rotel tomatoes & chilies'),('I167','tomatoes'),('I168','quinoa'),('I169','skinless chicken pieces'),('I17','light olive oil'),('I170','refried beans'),('I171','cinnamon raisin bread'),('I172','mayonnaise'),('I173','beef roast'),('I174','italian salad dressing mix'),('I175','fish fillets'),('I176','self-rising flour'),('I177','sweet potatoes'),('I178','almond butter'),('I179','canned pumpkin'),('I18','cream cheese'),('I180','firm ripe tomatoes'),('I181','bacon drippings'),('I182','flour tortillas'),('I19','boneless skinless chicken breast'),('I2','prepared pizza crust'),('I20','cucumber'),('I21','wild mushrooms'),('I22','dried yellow peas'),('I23','white bread'),('I24','red wine vinegar'),('I25','old fashioned oats'),('I26','boneless pork chops'),('I27','olive oil'),('I28','boneless skinless chicken breasts'),('I29','ground beef'),('I3','lean pork chops'),('I30','peanut oil'),('I31','egg'),('I32','kosher salt'),('I33','pumpkin'),('I34','beef stew'),('I35','short-grain brown rice'),('I36','sesame oil'),('I37','buttermilk'),('I38','ground veal'),('I39','eggs'),('I4','egg roll wrap'),('I40','canola oil'),('I41','ham bone'),('I42','sharp cheddar cheese'),('I43','boneless country-style ribs'),('I44','refrigerated biscuits'),('I45','sugar'),('I46','orange juice'),('I47','new potatoes'),('I48','plain tomato juice'),('I49','dry white wine'),('I5','water'),('I50','dry vermouth'),('I51','vital wheat gluten'),('I52','butter'),('I53','rice milk'),('I54','lump crabmeat'),('I55','southern comfort'),('I56','bacon'),('I57','rice'),('I58','rhodes frozen rolls'),('I59','minute rice'),('I6','onion'),('I60','baby portabella mushrooms'),('I61','broccoli'),('I62','plum tomatoes'),('I63','yakisoba noodles'),('I64','whole wheat bread'),('I65','cream'),('I66','round sourdough loaf'),('I67','salmon fillets'),('I68','chicken carcasses'),('I69','rice vinegar'),('I7','ground black pepper'),('I70','chickpeas'),('I71','unbleached flour'),('I72','jalapeno peppers'),('I73','crescent roll dough'),('I74','chicken breasts'),('I75','salmon fillet'),('I76','active dry yeast'),('I77','frozen meatballs'),('I78','ritz cracker crumbs'),('I79','fresh mushrooms'),('I8','red potatoes'),('I80','potato'),('I81','baking potatoes'),('I82','all-purpose flour'),('I83','knorr vegetable soup mix'),('I84','salsa'),('I85','red beans'),('I86','sugar pumpkin'),('I87','angostura bitters'),('I88','vegetable oil'),('I89','bourbon'),('I9','ground venison'),('I90','white creme de cacao'),('I91','oil'),('I92','lean ground beef'),('I93','mushrooms'),('I94','amaretto'),('I95','cauliflower florets'),('I96','chorizo sausages'),('I97','eggplant'),('I98','hot pork sausage'),('I99','fish');
/*!40000 ALTER TABLE `recipe_ingredient` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-17  2:32:27

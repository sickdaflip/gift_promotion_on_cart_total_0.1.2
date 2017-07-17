<?php

$installer = $this;

$installer->startSetup();

$installer->run("
        
         -- DROP TABLE IF EXISTS {$this->getTable('productpromotion')};
        CREATE TABLE {$this->getTable('productpromotion')} (
        `productpromotion_id` int(11) unsigned NOT NULL auto_increment,
        PRIMARY KEY (`productpromotion_id`)
        ) ENGINE=InnoDB;
        
    ");


$installer->run("
        -- DROP TABLE IF EXISTS {$this->getTable('productpromotion_data')};
        CREATE TABLE {$this->getTable('productpromotion_data')} (
	`id` int(11) unsigned NOT NULL auto_increment,
        `productpromotion_id` int(11) unsigned  NOT NULL,
        `title` varchar(255) NOT NULL default '',
        `promotion_price` decimal(12,2) NOT NULL,
	`product_id` varchar(255) NOT NULL default '',
        `store_id` smallint(5) unsigned NOT NULL default '0',
        `status` smallint(6) NOT NULL default '0',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB;
        
        ALTER TABLE `{$this->getTable('productpromotion_data')}` ADD INDEX ( `productpromotion_id` );
            
        ALTER TABLE `{$this->getTable('productpromotion_data')}` ADD FOREIGN KEY ( `productpromotion_id` ) REFERENCES `{$this->getTable('productpromotion')}` (`productpromotion_id`) ON DELETE CASCADE ON UPDATE CASCADE ;

        ALTER TABLE  `{$this->getTable('sales_flat_quote')}` ADD  `promotion_availed` int(11) NULL;
        
        ALTER TABLE  `{$this->getTable('sales_flat_quote')}` ADD  `promotion_availed_id` int(11) NULL;
        
        ALTER TABLE  `{$this->getTable('sales_flat_quote')}` ADD  `promotion_gift` int(11) NULL;
                    
");
$installer->endSetup();



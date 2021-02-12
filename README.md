# DNAFactory DataSeeder

This module helps you to save and import some of magento 2 entities (such as Blocks, Pages, Configurations and Email).

Before use it, you have to cpyt the content of 'assets' folder in your project root folder.
The structure should be exactly the same:
<pre>
- app
- pub
- ...
- assets
    - seeder
        - blocks
            - contents
        - configs
        - dumps
        - pages
            - contents
</pre>

### Config Import

There is an import command for all the listed entities.

To import the configuration you have to use:
<pre>bin/magento dnafactory:seeder:import-config environment config-filename</pre> 

There are two types of config:
- environment config
- shared config

the *environment config* contains only the configuration that should be set for a specific environment, such as 'production'.
For this the **environment** param should be *config-filename*.environment.php (ex: config.production.php)

the *shared config* instead, contains the configuration shared between all the environments.
In the command you can specify the filename. (ex. and default: config.php)
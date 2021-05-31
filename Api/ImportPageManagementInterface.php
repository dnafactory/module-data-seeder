<?php
namespace DNAFactory\DataSeeder\Api;

interface ImportPageManagementInterface 
{
    public function import($filename = "pages.php");
}

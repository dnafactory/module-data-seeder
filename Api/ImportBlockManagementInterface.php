<?php
namespace DNAFactory\DataSeeder\Api;

interface ImportBlockManagementInterface 
{
    public function import($fileName = "blocks.php");
}

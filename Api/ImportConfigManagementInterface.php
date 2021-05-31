<?php
namespace DNAFactory\DataSeeder\Api;

interface ImportConfigManagementInterface
{
    public function import($fileName = "config", $environment = "production");
}

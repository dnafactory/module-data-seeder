<?php
namespace DNAFactory\DataSeeder\Api;

interface ImportEmailManagementInterface 
{
    public function import($sqlFilename = "email_template.sql");
}

<?php

namespace DNAFactory\DataSeeder\Asset;

use Magento\Framework\App\ResourceConnection;

class EmailTemplateDiscover
{
    /**
     * @var ResourceConnection
     */
    protected $connection;

    public function __construct
    (
        ResourceConnection $connection
    )
    {
        $this->connection = $connection;
    }

    public function getTemplateIdByNameAndCode($name, $code = null)
    {
        try {
            $connection  = $this->connection->getConnection();
            $tableName = $connection->getTableName('email_template');

            $select = $connection->select()
                ->from($tableName, ['template_id'])
                ->where('template_code LIKE ?', '%' . $name .'%');

            if ($code === null) {
                $select = $select->where('orig_template_code IS NULL');
            } else {
                $select = $select->where('orig_template_code LIKE ?', $code);
            }

            $data = $connection->fetchAll($select);

            return $data[0]['template_id'];
        } catch (\Exception $exception) {
            return null;
        }
    }
}

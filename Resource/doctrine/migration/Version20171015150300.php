<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Doctrine\ORM\EntityManager;

/**
 * Class Version20171015150300.
 */
class Version20171015150300 extends AbstractMigration
{
    /**
     * @var string table name
     */
    const NAME = 'plg_kaiu_config';

    /**
     * @var array table eitity
     */
    protected $entities = array(
        'Plugin\KaiUConnection\Entity\Config',
    );

    /**
     * @var array sequence
     */
    protected $sequence = array(
        'config_id_seq',
    );

    /**
     * Setup table.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createTable($schema);
    }

    /**
     * remove table.
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $app = Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);
        $tool = new SchemaTool($app['orm.em']);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);

        foreach ($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }

        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }

        // For delete sequence in postgresql
        if ($this->connection->getDatabasePlatform()->getName() == 'postgresql') {
            foreach ($this->sequence as $sequence) {
                if ($schema->hasSequence($sequence)) {
                    $schema->dropSequence($sequence);
                }
            }
        }
    }

    /**
     * create table
     *
     * @param Schema $schema
     *
     * @return true
     */
    protected function createTable(Schema $schema)
    {
        if ($schema->hasTable(self::NAME)) {
            return true;
        }

        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array(
            $em->getClassMetadata(array_shift($this->entities)),
        );
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);

        return true;
    }

    /**
     * Get metadata.
     *
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }
}

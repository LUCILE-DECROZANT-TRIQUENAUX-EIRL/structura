<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use App\Entity\Receipt;

final class Version20200515180231 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return 'Add order_code field';
    }

    public function postUp(Schema $schema) : void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $receipts = $em->getRepository(Receipt::class)->findAll();

        foreach ($receipts as $receipt)
        {
            $receipt->generateOrderCode();
            $em->persist($receipt);
        }

        $em->flush();
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipt ADD order_code VARCHAR(9) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE receipt DROP order_code');
    }
}

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

        $receiptRepository = $em->getRepository(Receipt::class);
        $receipts = $receiptRepository->findByAllForVersion20200515180231();

        foreach ($receipts as $receipt)
        {
            if (empty($receipt['fiscal_year']))
            {
                $message = 'The year of this receipt is missing';
                throw new \Exception($message);
            }

            if (empty($receipt['order_number']))
            {
                $message = 'The order number of this receipt is missing';
                throw new \Exception($message);
            }

            $numberOfDigits = floor(log10((int) $receipt['order_number']) + 1);
            $numberOf0ToAdd = 4 - $numberOfDigits;
            $orderNumberPart = str_repeat('0', (int) $numberOf0ToAdd) . $receipt['order_number'];

            $orderCode = $receipt['fiscal_year'] . '-' . $orderNumberPart;

            $receiptRepository->updateOrderCodeForVersion20200515180231($receipt['id'], $orderCode);
        }

        $receipts = $receiptRepository->findByAllForVersion20200515180231();
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

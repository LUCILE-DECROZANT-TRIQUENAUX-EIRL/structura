<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use App\Entity\Payment;
use App\Entity\Receipt;

final class Version20200511151231 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return 'Generate receipts corresponding to existing payments';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Add SQL to avoid warning messages thrown by Symfony
        $this->addSql("SELECT 1;");

        $em = $this->container->get('doctrine.orm.entity_manager');
        $paymentRepository = $em->getRepository(Payment::class);

        $payments = $paymentRepository->findAll();

        // Desc order payments by their received date
        usort($payments, function($payment1, $payment2) {
            return $payment1->getDateReceived() > $payment2->getDateReceived();
        });

        // Sort payments by year
        $paymentsSortedByYear = [];
        foreach ($payments as $payment)
        {
            $year = $payment->getDateReceived()->format('Y');
            $paymentsSortedByYear[$year][] = $payment;
        }

        // Generate receipts using payments data
        foreach ($paymentsSortedByYear as $year => $payments)
        {
            $i = 1;
            foreach ($payments as $payment)
            {
                $receipt = new Receipt();
                $receipt->setPayment($payment);
                $receipt->setFiscalYear($year);
                $receipt->setOrderNumber($i++);
                $em->persist($receipt);
            }
        }
        $em->flush();
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM receipt');
    }
}

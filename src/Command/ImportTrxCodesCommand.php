<?php


namespace App\Command;


use App\Entity\HotelGroup;
use App\Entity\TrxCode;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTrxCodesCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:importTrxCodes';

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import CSV TRX Codes File.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command is used to import trx codes via CSV file to a hotel group.')
            // command's arguments
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the file')
            ->addArgument('groupName', InputArgument::REQUIRED, 'Which hotel group do you want to use?')
            ->addArgument('specificHotel', InputArgument::REQUIRED, 'Which hotel of the group do you want to edit?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $file = $input->getArgument('file');
        $groupName = $input->getArgument('groupName');
        $hotelName = $input->getArgument('specificHotel');
        $output->writeln('Read file input: '. $file);
        $now = new DateTime();

        /** @var HotelGroup $hotelGroup */
        $hotelGroup = $this->entityManager
            ->getRepository(HotelGroup::class)
            ->findOneBy(
                array(
                    'groupName' => $groupName
                )
            );
        $fileResource = fopen($file, 'r');

        //Headers
        $fileContent = fgetcsv($fileResource, 4096, ';');

        while($fileContent = fgetcsv($fileResource, 4096, ';')){
            /** @var TrxCode $trxCode */
            $trxCode = $this->entityManager
                ->getRepository(TrxCode::class)
                ->findOneBy(
                    array(
                        'trxCode' => trim($fileContent[19]),
                        'resort' => $hotelName
                    )
                );
            if(empty($trxCode)){
                $transactionCategory = $this->getTransactionCategory($fileContent[2]);
                if($transactionCategory != NULL) {
                    $trxCode = new TrxCode();
                    $trxCode->setHotelGroup($hotelGroup);
                    $trxCode->setResort($hotelName);
                    $trxCode->setCreated($now->format("U"));
                    $trxCode->setTrxCode($fileContent[19]);
                    $trxCode->setTransactionCategory($transactionCategory);
                    $trxCode->setTcGroup($fileContent[1]);
                    $trxCode->setTcSubgroup($fileContent[10]);
                    $trxCode->setD1($fileContent[4]);
                    $trxCode->setD2($fileContent[11]);
                    $trxCode->setD3($fileContent[20]);
                    $trxCode->setAdjTrx($fileContent[23]);
                    $this->entityManager->persist($trxCode);
                    $this->entityManager->flush();
                }
            } else {
                $transactionCategory = $this->getTransactionCategory($fileContent[2]);
                if($trxCode->getTrxCode() != $fileContent[19]){
                    $output->writeln('Different TRX code stored: '. $trxCode->getTrxCode() . ", new: $fileContent[19]");
                } elseif($trxCode->getTransactionCategory() != $transactionCategory){
                    $output->writeln('Different TRX Category for ' . $trxCode->getTrxCode() . ' stored: '. $trxCode->getTransactionCategory() . ", new: $transactionCategory");
                    $trxCode->setModified($now->format("U"));
                    $trxCode->setTrxCode($fileContent[19]);
                    $trxCode->setTransactionCategory($transactionCategory);
                    $trxCode->setTcGroup($fileContent[1]);
                    $trxCode->setTcSubgroup($fileContent[10]);
                    $trxCode->setD1($fileContent[4]);
                    $trxCode->setD2($fileContent[11]);
                    $trxCode->setD3($fileContent[20]);
                    $this->entityManager->persist($trxCode);
                    $this->entityManager->flush();
                }
            }
        }
    }

    private function getTransactionCategory($transactionCategory): ?string
    {
        return match ($transactionCategory) {
            "F&B" => "fnb",
            "Extras" => "other",
            "Accommodation" => "room",
            default => NULL,
        };
    }
}
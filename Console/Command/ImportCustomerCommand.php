<?php
namespace VML\CustomerImport\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use VML\CustomerImport\Model\Import\ImporterFactory;

class ImportCustomerCommand extends Command
{
    protected $importerFactory;

    public function __construct(
        ImporterFactory $importerFactory
    ) {
        $this->importerFactory = $importerFactory;
        parent::__construct('customer:import');
    }

    protected function configure()
    {
        $this->setName('customer:import')
            ->setDescription('Import customers from a file')
            ->addArgument('profile-name', InputArgument::REQUIRED, 'Profile Name')
            ->addArgument('source', InputArgument::REQUIRED, 'Source File');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profile = $input->getArgument('profile-name');
        $source = $input->getArgument('source');
        
        try {
            $importer = $this->importerFactory->create($profile);
            $importer->import($source);
            $output->writeln('<info>Customers imported successfully.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
        
        return Cli::RETURN_SUCCESS;
    }
}
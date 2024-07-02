<?php
namespace VML\CustomerImport\Model\Import;

use Magento\Framework\Exception\LocalizedException;


class ImporterFactory
{
    /**
     * @var array
     */
    private $importers;

    /**
     * ImporterFactory constructor.
     * @param array $importers
     */
    public function __construct(array $importers)
    {
        $this->importers = $importers;
    }

    /**
     * Create importer instance based on profile name.
     *
     * @param string $profile
     * @return ImporterInterface
     * @throws LocalizedException
     */
    public function create($profile)
    {
      
        if (!isset($this->importers[$profile])) {
            throw new LocalizedException(__('Importer profile "%1" is not defined.', $profile));
        }

        return $this->importers[$profile];
    }
}

<?php
namespace VML\CustomerImport\Model\Import;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use VML\CustomerImport\Model\ImporterInterface;

class CsvImporter implements ImporterInterface
{
    protected $customerFactory;
    protected $customerRepository;
    protected $addressFactory;
    protected $storeManager;

    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        AddressInterfaceFactory $addressFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->addressFactory = $addressFactory;
        $this->storeManager = $storeManager;
    }

    public function import($source)
    {
        $csvData = $this->readCsv($source);
        $this->saveCustomers($csvData);
    }

    protected function readCsv($filePath)
    {
        $rows = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    protected function saveCustomers(array $data)
    {
        $headers = array_shift($data); // Assuming the first row is the header

        foreach ($data as $row) {
            $customerData = array_combine($headers, $row);

            try {
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($this->storeManager->getWebsite()->getId());
                $customer->setEmail($customerData['emailaddress']);
                $customer->setFirstname($customerData['fname']);
                $customer->setLastname($customerData['lname']);
                $customer->setGroupId(1); // General customer group

                $this->customerRepository->save($customer);

                if (!empty($customerData['address'])) {
                    $address = $this->addressFactory->create();
                    $address->setCustomerId($customer->getId());
                    $address->setFirstname($customer->getFirstname());
                    $address->setLastname($customer->getLastname());
                    $address->setStreet([$customerData['address']]);
                    $address->setCity($customerData['city']);
                    $address->setCountryId('US'); // Country code
                    $address->setPostcode($customerData['postcode']);
                    $address->setTelephone($customerData['telephone']);
                    $address->setIsDefaultBilling(true);
                    $address->setIsDefaultShipping(true);
                    
                    $this->customerRepository->save($customer, $address);
                }

            } catch (LocalizedException $e) {
                // Handle exception if customer cannot be saved
            }
        }
    }
}

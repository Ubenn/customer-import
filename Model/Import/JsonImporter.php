<?php
namespace VML\CustomerImport\Model\Import;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use VML\CustomerImport\Model\ImporterInterface;

class JsonImporter implements ImporterInterface
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
        $jsonData = $this->readJson($source);
        $this->saveCustomers($jsonData);
    }

    protected function readJson($filePath)
    {
        $jsonData = file_get_contents($filePath);
        return json_decode($jsonData, true);
    }

    protected function saveCustomers(array $data)
    {
        foreach ($data as $customerData) {
            try {
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($this->storeManager->getWebsite()->getId());
                $customer->setEmail($customerData['emailaddress']);
                $customer->setFirstname($customerData['fname']);
                $customer->setLastname($customerData['lname']);
                $customer->setGroupId(1); // General customer group

                $this->customerRepository->save($customer);
 
                // If you have address data, you can add it like this:
                if (!empty($customerData['address'])) {
                    $address = $this->addressFactory->create();
                    $address->setCustomerId($customer->getId());
                    $address->setFirstname($customer->getFname());
                    $address->setLastname($customer->getLname());
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
                // For example, log the error or skip the customer
            }
        }
    }
}

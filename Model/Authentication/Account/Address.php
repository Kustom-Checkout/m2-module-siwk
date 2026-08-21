<?php
/**
 * Copyright 2025 Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Model\Authentication\Account;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\RegionFactory;

/**
 * @internal
 */
class Address
{
    /**
     * @var AddressInterfaceFactory
     */
    private AddressInterfaceFactory $factory;
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $repository;
    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;

    /**
     * @param AddressInterfaceFactory $factory
     * @param AddressRepositoryInterface $repository
     * @param RegionFactory $regionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        AddressInterfaceFactory $factory,
        AddressRepositoryInterface $repository,
        RegionFactory $regionFactory
    ) {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Adding the address to the customer
     *
     * @param CustomerInterface $customer
     * @param array $data
     * @return CustomerInterface
     */
    public function add(CustomerInterface $customer, array $data): CustomerInterface
    {
        $billingAddress = $data['billing_address'];
        $country = (string)($billingAddress['country'] ?? '');

        $address = $this->factory->create();
        $address->setCountryId($country);
        $address->setFirstname($data['given_name']);
        $address->setLastname($data['family_name']);
        $address->setTelephone($data['phone']);
        $this->applyRegion($address, $billingAddress['region'] ?? null, $country);
        $address->setCity($billingAddress['city']);
        $address->setPostcode($billingAddress['postal_code']);
        $address->setCustomerId($customer->getId());
        $address->setStreet([$billingAddress['street_address']]);

        if (empty($customer->getAddresses())) {
            $address->setIsDefaultShipping(true);
            $address->setIsDefaultBilling(true);
        }

        $this->repository->save($address);

        return $customer;
    }

    /**
     * Applying the region on the customer address
     *
     * Kustom sends the region as a free text value (or not at all for countries without regions). Assigning that raw
     * value to region_id produces an invalid address which Magento 2.4.8 rejects, so it has to be resolved first.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @param mixed $region
     * @param string $country
     */
    private function applyRegion($address, $region, string $country): void
    {
        $region = is_string($region) ? trim($region) : $region;

        if ($region === null || $region === '' || $country === '') {
            return;
        }

        $regionModel = $this->regionFactory->create()->loadByCode($region, $country);

        if (!$regionModel->getId()) {
            $regionModel = $this->regionFactory->create()->loadByName($region, $country);
        }

        if ($regionModel->getId()) {
            $address->setRegionId((int)$regionModel->getId());
        }
    }

    /**
     * Returns true if the address already exists in the address book
     *
     * @param CustomerInterface $customer
     * @param array $klarnaData
     * @return bool
     */
    public function existInAddressBook(CustomerInterface $customer, array $klarnaData): bool
    {
        $fields = [
            'given_name' => 'firstname',
            'family_name' => 'lastname',
            'phone' => 'telephone',
            'billing_address' => [
                'country' => 'country_id',
                'region' => 'region_id',
                'city' => 'city',
                'postal_code' => 'postcode',
                'street_address' => 'street'
            ]
        ];

        if (count($customer->getAddresses()) === 0) {
            return false;
        }

        foreach ($customer->getAddresses() as $address) {
            $customerAddressData = $address->__toArray();
            $customerAddressData['street'] = implode(' ', $customerAddressData['street']);

            foreach ($fields as $topFieldKey => $topFieldValue) {
                if (is_array($topFieldValue)) {
                    foreach ($topFieldValue as $innerFieldKey => $innerFieldValue) {
                        if ($this->isDifferentValue(
                            $customerAddressData[$innerFieldValue],
                            $klarnaData[$topFieldKey][$innerFieldKey]
                        )) {
                            continue 3;
                        }
                    }
                    continue;
                }

                if ($this->isDifferentValue($customerAddressData[$topFieldValue], $klarnaData[$topFieldKey])) {
                    continue 2;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Returns true if the value is different
     *
     * @param string|null $customerAddressValue
     * @param string|null $klarnaAddressValue
     * @return bool
     */
    private function isDifferentValue(?string $customerAddressValue, ?string $klarnaAddressValue): bool
    {
        $customerAddressValue = $this->normalise($customerAddressValue);
        $klarnaAddressValue = $this->normalise($klarnaAddressValue);

        return $klarnaAddressValue !== null &&
            $klarnaAddressValue !== $customerAddressValue;
    }

    /**
     * Normalising the value
     *
     * @param string|null $value
     * @return string|null
     */
    private function normalise(?string $value): ?string
    {
        if ($value === null) {
            return $value;
        }

        $value = strtolower($value);
        return trim($value);
    }
}

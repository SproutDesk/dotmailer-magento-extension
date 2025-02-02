<?php

class Dotdigitalgroup_Email_Model_Catalog_Urlfinder
{
    /**
     * Fetch a URL for a product depending on its visibility and type.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|string|null $storeId
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function fetchFor($product, $storeId = null)
    {
        $product = $this->getScopedProduct($product, $storeId);

        if (
            $product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
            && $product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
            && $parentProduct = $this->getParentProduct($product)
        ) {
            return $parentProduct->getProductUrl();
        }

        return $product->getProductUrl();
    }

    /**
     * Set the correct store scope for a product, in cases where it is not already set.
     * Achieve this either by manually supplying a store ID, or by finding the default store ID when one is not supplied.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|string|null $storeId
     *
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Exception
     */
    private function getScopedProduct($product, $storeId = null)
    {
        if (empty($storeId) && in_array($product->getStoreId(), $product->getStoreIds())) {
            return $product;
        }

        // If $storeId is empty or 0, assign the default store ID
        if (empty($storeId) && !in_array($product->getStoreId(), $product->getStoreIds())) {
            $productInWebsites = $product->getWebsiteIds();
            $firstWebsite = Mage::app()->getWebsite($productInWebsites[0]);
            $storeId = (int) $firstWebsite->getDefaultGroup()->getDefaultStoreId();
        }

        return Mage::getModel('catalog/product')
            ->load($product->getId())
            ->setStoreId($storeId);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Mage_Catalog_Model_Product|null
     */
    private function getParentProduct($product)
    {
        if ($parentId = $this->getFirstParentId($product)) {
            return Mage::getModel('catalog/product')
                ->load($parentId)
                ->setStoreId($product->getStoreId());
        }
        return null;
    }

    /**
     * Return parent ID for configurable, grouped or bundled products (in that order of priority)
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return mixed
     */
    private function getFirstParentId($product)
    {
        $configurableProducts = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($product->getId());
        if (isset($configurableProducts[0])) {
            return $configurableProducts[0];
        }

        $groupedProducts = Mage::getModel('catalog/product_type_grouped')
            ->getParentIdsByChild($product->getId());
        if (isset($groupedProducts[0])) {
            return $groupedProducts[0];
        }

        $bundleProducts = Mage::getResourceSingleton('bundle/selection')
            ->getParentIdsByChild($product->getId());
        if (isset($bundleProducts[0])) {
            return $bundleProducts[0];
        }

        return null;
    }
}

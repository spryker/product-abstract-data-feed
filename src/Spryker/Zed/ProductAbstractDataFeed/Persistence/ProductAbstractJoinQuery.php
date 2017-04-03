<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAbstractDataFeed\Persistence;

use Generated\Shared\Transfer\ProductAbstractDataFeedTransfer;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Propel\Runtime\ActiveQuery\Criteria;

class ProductAbstractJoinQuery
{

    const LOCALE_FILTER_VALUE = 'LOCALE_FILTER_VALUE';
    const LOCALE_FILTER_CRITERIA = 'LOCALE_FILTER_CRITERIA';

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    public function applyJoins(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        $abstractProductQuery = $this->joinProductLocalizedAttributes($abstractProductQuery, $abstractProductDataFeedTransfer);
        $abstractProductQuery = $this->joinProductImages($abstractProductQuery, $abstractProductDataFeedTransfer);
        $abstractProductQuery = $this->joinProductCategories($abstractProductQuery, $abstractProductDataFeedTransfer);
        $abstractProductQuery = $this->joinProductPrices($abstractProductQuery, $abstractProductDataFeedTransfer);
        $abstractProductQuery = $this->joinConcreteProducts($abstractProductQuery, $abstractProductDataFeedTransfer);
        $abstractProductQuery = $this->joinProductOptions($abstractProductQuery, $abstractProductDataFeedTransfer);

        return $abstractProductQuery;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinProductImages(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        if (!$abstractProductDataFeedTransfer->getIsJoinImage()) {
            return $abstractProductQuery;
        }
        $localeTransferConditions = $this->getIdLocaleFilterConditions($abstractProductDataFeedTransfer->getLocaleId());

        $abstractProductQuery
            ->useSpyProductImageSetQuery(null, Criteria::LEFT_JOIN)
                ->filterByFkLocale(
                    $localeTransferConditions[self::LOCALE_FILTER_VALUE],
                    $localeTransferConditions[self::LOCALE_FILTER_CRITERIA]
                )
                ->useSpyProductImageSetToProductImageQuery(null, Criteria::LEFT_JOIN)
                    ->leftJoinSpyProductImage()
                ->endUse()
            ->endUse();

        return $abstractProductQuery;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinProductCategories(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        if (!$abstractProductDataFeedTransfer->getIsJoinCategory()) {
            return $abstractProductQuery;
        }
        $localeTransferConditions = $this->getIdLocaleFilterConditions($abstractProductDataFeedTransfer->getLocaleId());

        $abstractProductQuery
            ->useSpyProductCategoryQuery(null, Criteria::LEFT_JOIN)
                ->useSpyCategoryQuery(null, Criteria::LEFT_JOIN)
                    ->useAttributeQuery(null, Criteria::LEFT_JOIN)
                        ->filterByFkLocale(
                            $localeTransferConditions[self::LOCALE_FILTER_VALUE],
                            $localeTransferConditions[self::LOCALE_FILTER_CRITERIA]
                        )
                    ->endUse()
                ->endUse()
            ->endUse();

        return $abstractProductQuery;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinProductPrices(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        if (!$abstractProductDataFeedTransfer->getIsJoinPrice()) {
            return $abstractProductQuery;
        }
        $abstractProductQuery
            ->usePriceProductQuery(null, Criteria::LEFT_JOIN)
                ->joinPriceType()
            ->endUse();

        return $abstractProductQuery;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinConcreteProducts(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        if (!$abstractProductDataFeedTransfer->getIsJoinProduct()) {
            return $abstractProductQuery;
        }
        $localeTransferConditions = $this->getIdLocaleFilterConditions($abstractProductDataFeedTransfer->getLocaleId());

        $abstractProductQuery
            ->useSpyProductQuery(null, Criteria::LEFT_JOIN)
                ->useSpyProductLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
                    ->filterByFkLocale(
                        $localeTransferConditions[self::LOCALE_FILTER_VALUE],
                        $localeTransferConditions[self::LOCALE_FILTER_CRITERIA]
                    )
                ->endUse()
                ->useSpyProductImageSetQuery()
                    ->useSpyProductImageSetToProductImageQuery()
                        ->leftJoinSpyProductImage()
                    ->endUse()
                ->endUse()
            ->endUse();

        return $abstractProductQuery;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinProductOptions(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        if (!$abstractProductDataFeedTransfer->getIsJoinOption()) {
            return $abstractProductQuery;
        }
        $abstractProductQuery
            ->useSpyProductAbstractProductOptionGroupQuery(null, Criteria::LEFT_JOIN)
                ->useSpyProductOptionGroupQuery(null, Criteria::LEFT_JOIN)
                    ->leftJoinSpyProductOptionValue()
                ->endUse()
            ->endUse();

        return $abstractProductQuery;
    }

    /**
     * @param integer|null $localeId
     *
     * @return array
     */
    protected function getIdLocaleFilterConditions($localeId = null)
    {
        if ($localeId !== null) {
            $filterCriteria = Criteria::EQUAL;
            $filterValue = $localeId;
        } else {
            $filterCriteria = Criteria::NOT_EQUAL;
            $filterValue = null;
        }

        return [
            self::LOCALE_FILTER_VALUE => $filterValue,
            self::LOCALE_FILTER_CRITERIA => $filterCriteria,
        ];
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $abstractProductQuery
     * @param \Generated\Shared\Transfer\ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected function joinProductLocalizedAttributes(
        SpyProductAbstractQuery $abstractProductQuery,
        ProductAbstractDataFeedTransfer $abstractProductDataFeedTransfer
    ) {
        $localeTransferConditions = $this->getIdLocaleFilterConditions($abstractProductDataFeedTransfer->getLocaleId());

        $abstractProductQuery
            ->useSpyProductAbstractLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
                ->filterByFkLocale(
                    $localeTransferConditions[self::LOCALE_FILTER_VALUE],
                    $localeTransferConditions[self::LOCALE_FILTER_CRITERIA]
                )
            ->endUse();

        return $abstractProductQuery;
    }

}

<?php

namespace Denngarr\Seat\Fitting\Helpers;

use Denngarr\Seat\Fitting\Exceptions\FittingParserBadFormatException;
use Denngarr\Seat\Fitting\Item\PriceableEveItem;
use Illuminate\Support\Collection;
use RecursiveTree\Seat\PricesCore\Exceptions\PriceProviderException;
use RecursiveTree\Seat\PricesCore\Facades\PriceProviderSystem;
use RecursiveTree\Seat\TreeLib\Parser\Parser;
use Seat\Services\Exceptions\SettingException;

class FittingHelper
{
    /**
     * @throws FittingParserBadFormatException
     * @throws SettingException
     * @throws PriceProviderException
     */
    public static function parseEveFittingData(string $fitting): Collection
    {
        if (empty($fitting)) {
            throw new FittingParserBadFormatException(trans('fitting::global.error_empty_string_not_supported'));
        }

        $parser_result = Parser::parseItems($fitting, PriceableEveItem::class);

        if ($parser_result->items->isEmpty()) {
            throw new FittingParserBadFormatException(trans('fitting::global.error_provided_string_not_parsable'));
        }

        $priceProviderId = setting('fitting.admin_price_provider', true);

        if ($priceProviderId == null) {
            throw new PriceProviderException(trans('fitting::global.error_price_provider_not_configured'));
        }

        PriceProviderSystem::getPrices($priceProviderId, $parser_result->items);

        return $parser_result->items;
    }

    public static function toFittingEvaluation(Collection $items): array
    {
        $result = [
            'total' => 0,
            'volume' => 0
        ];

        foreach ($items as $item) {
            $result['total'] += $item->price * $item->getAmount();
            $result['volume'] += $item->typeModel->volume * $item->getAmount();
        }

        return $result;
    }

    public static function toFittingIcon($fit): string
    {
        $typeId = $fit['typeID'];

        return config('fitting.config.eve.imageServerUrl') . $typeId . "/icon?size=32";
    }
}
<?php

namespace Sherlockode\SyliusNorbrPlugin\Norbr;

/**
 * Class ApiCode
 */
abstract class ApiCode
{
    public const TRANSACTION_SUCCESSFUL = 'transaction_successful';
    public const TRANSACTION_FAILED = 'transaction_failed';

    /**
     * @param int $code
     *
     * @return string|null
     */
    public static function getCodeDescription(int $code): ?string
    {
        $codes = [
            111011 => 'missing required parameter',
            111012 => 'invalid parameter',
            111013 => 'an error occurred',
            111022 => 'invalid API key',
            111023 => 'request forbidden due to limited credential rights - internal',
            111034 => 'max attempts reached',
            111035 => 'order is already being processed',
            131010 => 'blacklist',
            131020 => 'mismatch',
            131030 => 'external check(AVS, missing 3D, device fingerprint, etc .)',
            131040 => 'country not supported',
            131050 => 'velocity',
            131051 => 'card velocity',
            131052 => 'email velocity',
            131053 => 'IP velocity',
            131054 => 'verified info',
            131055 => 'bin velocity',
            131056 => 'billing address velocity',
            131057 => 'shipping address velocity',
            131058 => 'cardholder name velocity',
            131059 => 'custom data velocity',
            131088 => 'risk blocked transaction',
            133333 => 'risk approved',
            151021 => 'invalid psp credentials',
            151023 => 'request forbidden due to limited credential rights - external',
            151040 => 'no route available for the requested payment method',
            151041 => 'no route available for the requested currency',
            151042 => 'no route available for the requested channel',
            151043 => 'no route available for the requested partner',
            161000 => 'client dropped off',
            240020 => 'invalid account',
            240021 => 'invalid account - card number',
            240022 => 'invalid account - card expired',
            241021 => 'token expired',
            241031 => 'invalid card information',
            261000 => 'client dropped off',
            320001 => '3DS timeout',
            320120 => 'card not enrolled',
            320310 => 'ACS server unavailable',
            341000 => 'incorrect authentication',
            361000 => 'client dropped off',
            404000 => 'route does not exist',
            410070 => 'integration error',
            420030 => 'risk rejection',
            420040 => 'unsupported currency',
            420041 => 'unsupported payment method',
            420050 => '3DS required',
            420088 => 'declined',
            420100 => 'insufficient funds',
            420110 => 'issuer server unavailable',
            420111 => 'max invalid attempts reached',
            420130 => 'risk rejection',
            420150 => 'soft decline - 3DS required',
            420188 => 'declined by issuer',
            420210 => 'acquirer server unavailable',
            420230 => 'risk rejection',
            420240 => 'invalid merchant ID configuration',
            420241 => 'unsupported currency',
            420242 => 'unsupported payment method',
            420248 => 'error in merchant configuration',
            420250 => '3DS required',
            420260 => 'invalid amount',
            420288 => 'declined by acquirer',
            420410 => 'PSP server unavailable',
            421080 => 'pending authorization',
            421081 => 'declined',
            421101 => 'threshold limit',
            421110 => 'issuer server unavailable',
            421120 => 'unsupported currency',
            421125 => 'card expired',
            421126 => 'restricted card',
            421127 => 'operation not allowed',
            421130 => 'risk rejection',
            421131 => 'stolen card',
            421132 => 'lost card',
            440120 => 'invalid card - updated card available',
            441120 => 'invalid card',
            511030 => 'authorization already captured',
            511031 => 'capture amount exceeding authorization',
            511032 => 'duplicated capture request',
            511033 => 'too many capture requests',
            520120 => 'authorization expired',
            520220 => 'capture unavailable',
            521080 => 'pending capture request',
            521088 => 'capture declined',
            521100 => 'order cancelled by customer',
            521188 => 'capture declined',
            521260 => 'invalid amount',
            521270 => 'amount exceeding authorization',
            521271 => 'authorization already captured',
            521272 => 'duplicated capture request',
            521273 => 'too many capture requests',
            521288 => 'capture declined',
            611030 => 'refund amount exceeding captured amount',
            611031 => 'capture already refunded',
            611032 => 'duplicated refund request',
            611033 => 'too many refund requests',
            620210 => 'refund unavailable',
            621080 => 'pending refund request',
            621088 => 'refund declined',
            621120 => 'refund period expired',
            621260 => 'invalid amount',
            621261 => 'refund amount exceeding captured amount',
            621270 => 'capture already refunded',
            621271 => 'duplicated refund request',
            621272 => 'too many refund requests',
            621288 => 'refund declined',
            711030 => 'authorization expired',
            711031 => 'authorization already captured',
            711032 => 'authorization already cancelled',
            720010 => 'authorization cancellation unavailable',
            721088 => 'authorization cancellation declined',
            721170 => 'authorization expired',
            721171 => 'authorization already captured',
            721172 => 'authorization already cancelled',
            721188 => 'authorization cancellation declined',
            721288 => 'authorization cancellation declined',
            922222 => 'tokenization successful',
            944444 => 'authorization successful',
            955555 => 'capture successful',
            966666 => 'refund successful',
            977777 => 'cancel successful',
            988888 => 'settlement successful',
        ];

        return $codes[$code] ?? null;
    }

    /**
     * @return int[]
     */
    public static function getSuccessCodes(): array
    {
        return [
            922222, // tokenization successful
            944444, // authorization successful
            955555, // capture successful
            966666, // refund successful
            977777, // cancel successful
            988888, // settlement successful
        ];
    }
}

<?php
if (!defined('NGCMS')) {
    die('HAL');
}

function payments_adapter_redirect(int $orderId, array $order, array $cfg): void
{
    global $template, $twig;

    if (empty($cfg['enabled'])) {
        throw new RuntimeException('The test payment adapter is disabled.');
    }
    if (!payments_test_order_allowed($order)) {
        http_response_code(403);
        throw new RuntimeException('Test payment is available only to the signed-in order owner.');
    }

    $amount = trim((string)($order['total'] ?? ''));
    if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $amount) || (float)$amount <= 0) {
        throw new RuntimeException('The test payment order has an invalid amount.');
    }
    $currency = pluginGetVariable('payments', 'currency') ?: 'RUB';
    $actionUrl = generatePluginLink('payments', 'pay', [], [
        'order_id' => $orderId,
        'uniqid' => $order['uniqid'],
        'method' => 'test',
    ]);

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $token = $_POST['token'] ?? null;
        if (($_POST['action'] ?? '') !== 'confirm_test_payment'
            || !is_string($token)
            || !hash_equals((string)genUToken('payments.test.confirm'), $token)) {
            throw new RuntimeException('Invalid test payment confirmation token.');
        }

        payments_mark_paid(
            $orderId,
            'test',
            'sandbox-' . $orderId,
            ['sandbox' => true, 'order_id' => $orderId, 'amount' => $amount, 'currency' => $currency],
            $amount,
            $currency
        );
        \Plugins\redirect(generatePluginLink('payments', 'success', [], [
            'order_id' => $orderId,
            'uniqid' => $order['uniqid'],
        ]));
        return;
    }

    $paths = locatePluginTemplates(['test_checkout'], 'payments', 1);
    if (empty($paths['test_checkout'])) {
        throw new RuntimeException('The test payment checkout template could not be located.');
    }
    $view = $twig->loadTemplate($paths['test_checkout'] . '/test_checkout.tpl');
    $template['vars']['mainblock'] = $view->render([
        'order' => $order,
        'currency' => $currency,
        'action_url' => $actionUrl,
        'cancel_url' => generatePluginLink('payments', 'pay', [], [
            'order_id' => $orderId,
            'uniqid' => $order['uniqid'],
        ]),
        'token' => genUToken('payments.test.confirm'),
        'lang' => payments_lang_vars(),
    ]);
}

function payments_adapter_notify(array $data, array $cfg): void
{
    \Plugins\logger('Rejected external callback for test payment adapter.', 'warn', 'payments.log');
    http_response_code(405);
    echo 'Test payment adapter does not accept external callbacks.';
}
